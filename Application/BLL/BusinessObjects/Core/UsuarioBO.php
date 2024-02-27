<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\BusinessEnumerations\TipoClaveEnum;
use Application\BLL\BusinessEnumerations\TipoLogEnum;
use Application\BLL\DataTransferObjects\Core\LogDto;
use Application\BLL\Services\Core\RolPorPerfilSvc;
use Application\BLL\Services\Core\RolSvc;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\Services\Administracion\ClienteSvc;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\BLL\DataTransferObjects\Core\RolDto;
use Application\BLL\DataTransferObjects\Core\UsuarioActividadDto;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Viatico\MonUsuarioDto;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\DocumentFactory;
use Application\BLL\Document\ProcessTypeEnum;
use Application\BLL\Filters\CambiarDatosUsuarioFilterDto;
use Application\BLL\Filters\NuevoUsuarioFilterDto;
use Application\BLL\Services\Configuracion\TiendaSvc;
use Application\BLL\Services\Core\LogSvc;
use Application\BLL\Services\Core\UsuarioActividadSvc;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\BLL\Services\Mantenciones\DetencionSvc;
use Application\BLL\Services\Viatico\MonUsuarioSvc;
use Application\Configuration\ConnectionEnum;
use DateTime;
use Exception;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Configuration\MenuConfig;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\Widget\FaIcon;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc;

class UsuarioBO 
{

    public function LoadUsuarioFull(UsuarioDto $usuario) 
    {
        // Agregar perfil y funcionalidades autorizadas
        $rolPorPerfilSvc = new RolPorPerfilSvc(ConnectionEnum::CORE);
        $rolPorPerfil = $rolPorPerfilSvc->GetByPerfil($usuario->IdPerfil);

        $perfilSvc = new PerfilSvc(ConnectionEnum::CORE);
        $perfil = $perfilSvc->Find($usuario->IdPerfil);

        $perfil->Roles = array();

        $rolSvc = new RolSvc(ConnectionEnum::CORE);
        // inyectar roles
        if (isset($rolPorPerfil)) {
            foreach ($rolPorPerfil as $rol) {
                $rol = $rolSvc->Find($rol->IdRol);
                $perfil->Roles[$rol->IdRol] = $rol;
            }
        }

        $usuario->Perfil = $perfil;

        // Crear sesion para el tipo de producto
        $_SESSION['producto'] = $usuario->Cliente->TipoProducto;

        $logoDoc = DocumentFactory::CreateDocument(ProcessTypeEnum::PROCESS_SYSTEM, DocumentTypeEnum::LOGO, [
            'Cliente'   => $usuario->Cliente
        ]);
        $usuario->Logo = $logoDoc->GetUrl();
        $usuario->LogoHttp = $logoDoc->GetHttpUrl();

        // Generar funcionalidades para el usuario
        $funcs = MenuConfig::FilterUserMenu($GLOBALS['router'], Session::Instance()->idioma, Session::Instance()->locale);

        // Generar la estructura del menu widget
        $menu = MenuConfig::GetUserMenuWidget($funcs);

        $usuario->Funcionalidades = $funcs;
        $usuario->Menu = $menu;
        
        Session::Instance()->usuario = $usuario;

        return $usuario;
    }

    public function GetRoles (UsuarioDto|null $usuario) {

        $roles = [];

        if (!isset($usuario) || $usuario == null) {
            return [];
        }

        return $roles;
    }

    public function GetUsuario(int $IdUsuario){ 
 

        ## INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $UsuarioService     =   new UsuarioSvc(ConnectionEnum::CORE);

        try{
            ## BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $UsuarioService->FindByForeign('IdUsuario',$IdUsuario);

        } catch (\Exception $ex) {

            ## GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    public function UpdateUsuario(CambiarDatosUsuarioFilterDto $DatosUsuario){

        ## INSTANCIAMOS EL SERVICE DEL USUARIO
        $UsuarioSvc         =   new UsuarioSvc(ConnectionEnum::CORE);

        ## BUSCAMOS EL DTO DEL USUARIO
        $UsuarioDto         =   $UsuarioSvc->FindByForeign('IdUsuario',$DatosUsuario->IdUsuario);

        ## VALIDAMOS SI EL USUARIO EXISTEE DENTRO DE LA BBDD
        if ($UsuarioDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El usuario no se encuentra disponble');
        }

        ## PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{

            ## 
            $UsuarioDto->Nombre             =   $DatosUsuario->Nombre;
            $UsuarioDto->Sigla              =   strtoupper($DatosUsuario->Sigla); 
            $UsuarioDto->Cargo              =   $DatosUsuario->Cargo;
            $UsuarioDto->IdTipoUsuario      =   $DatosUsuario->IdTipoUsuario;
            $UsuarioDto->IdJefeDirecto      =   $DatosUsuario->IdJefeDirecto == -1 ? null : $DatosUsuario->IdJefeDirecto ; 
            
            ## ACTUALIZAMOS LOS VALORES EN LA BBDD
            $UsuarioSvc->Update($UsuarioDto);

            return true;


        } catch (\Exception $ex) {

            return false;

        }

    }
    
    public function ActivarDesactivarUsuario(int $IdUsuario) {

        ## -----------------------------------------------------------------------------
        ## ESTA FUNCIÓN NOS PERMITE ACTUALIZAR EL ESTADO ACTUAL DEL USUARIO
        ## PARA ALMACENAR EL ESTADO INVERSO EN LA BBDD
        ## -----------------------------------------------------------------------------

        ## instanciamos los services necesarios
        $UsuarioSvc         =   new UsuarioSvc(ConnectionEnum::CORE);
        $LogSvc             =   new LogSvc(ConnectionEnum::DEFAULT);

        ## obtenemos los datos del usuario activo
        $Session            =   Session::Instance()->usuario;

        ## -----------------------------------------------------------------------------
        ## una vez instanciados los SVC procedemos a generar un multi connection
        ## esto nos permitirá realizar un rollback en caso de error
        ## o realizar commit en caso de que todo el proceso sea exitoso 
        ## -----------------------------------------------------------------------------
        GenericSvc::BeginMultipleOperations(ConnectionEnum::CORE);



        try{

            ## buscamos el usaurio solicitado
            $UsuarioDto         =   $UsuarioSvc->FindByForeign('IdUsuario',$IdUsuario);

            ## validamos que la bbdd responda con el usuario indicado
            if ( $UsuarioDto == null ) {
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message: 'El usuario es invalido o tienes acceso a el');
            }

            ## luego procedemos a intercambiar los estados de esta cuenta

            ## en caso de que el usuario se encuentre eliminado
            ## procedemos a activarlo 
            $Log            =   "";
            $NuevoEstado    =   0;
            if ($UsuarioDto->Eliminado == 1){
                
                $UsuarioDto->Eliminado  =   0;
                $Log                    =   "El usuario id " . $UsuarioDto->LoginName . " ha sido activado por " . $Session->LoginName;

            }else{

                $NuevoEstado            =   1;
                $UsuarioDto->Eliminado  =   1;
                $Log                    =   "El usuario id " . $UsuarioDto->LoginName . " ha sido desactivado por " . $Session->LoginName;
            }

            ## guardamos los cambios en el usuario
            $UsuarioSvc->Update($UsuarioDto);

            $FechaModificación      = (new DateTime())->format('Y-m-d H:m:s');

            ## generamos el dto del nuevo log
            $LogDto     =   new LogDto(
                IdTipoLog           :   TipoLogEnum::CAMBIO_ESTADO_USUARIO,
                IdUsuario           :   $Session->IdUsuario,
                IdUsuarioAfectado   :   $UsuarioDto->IdUsuario,
                Descripcion         :   $Log,
                Fecha               :   $FechaModificación
            ) ;
                
            ## guardamos el log en la BBDD
            $LogSvc->Insert($LogDto);

            ## si llegamos a este punto entendemos que podemos guardar los cambios en la BBDD
            GenericSvc::SaveMultipleOperations();
            
            ## retornamos cual es el nuevo estado del usuario
            return $NuevoEstado;

        } catch (\Exception $ex) {
            ## si llegamos a este punto, quiere decir que en alguna parte del codigo
            ## se encuentra un error, por ende, debemos realizar un rollback de los cambios
            ## realizados en la BBDD.

            GenericSvc::UndoMultipleOperations();

            ## procedemos a retornar un false para indicar que ocurrio un error durante el proceso
            return false;
        }

    }

    public function CrearNuevoUsuario( NuevoUsuarioFilterDto $NuevoUsuario) : UsuarioDto|null  {

        ## EN PRIMER LUGAR PROCEDEMOS A REALIZAR LA VALIDACIÓN DE LOS DATOS 
        if (strlen($NuevoUsuario->Cargo) < 3){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS SI SE INGRESO EL TIPO DE USUARIO
        if ( $NuevoUsuario->IdTipoUsuario < 1){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS SI SE INGRESO EL USUARIO JEFE
        if ( $NuevoUsuario->IdUsuario < 1){
            $NuevoUsuario->IdUsuario    = null;
        }
        
        ## VALIDAMOS SI SE INGRESO EL TIPO DE USUARIO 
        $NuevoUsuario->LoginName = removeEspecialCharacters($NuevoUsuario->LoginName);
        if ( strlen($NuevoUsuario->LoginName) < 17){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'el correo es invalido o ya se encuentra utilizado');
        }

        ## VALIDAMOS EL NOMBRE DEL USUARIO
        if ( strlen($NuevoUsuario->Nombre) < 3){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS LA SIGLA
        if ( strlen($NuevoUsuario->Sigla) != 2){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        ## PROCEDEMOS A GENERAR EL NUEVO USUARIO

        $UsuarioSvc     =   new UsuarioSvc(ConnectionEnum::CORE);
        $LogSvc         =   new LogSvc(ConnectionEnum::CORE);
        $UsuarioLogin   =   Session::Instance()->usuario;


        $ValidUser      =   $UsuarioSvc->FindByForeign('LoginName',$NuevoUsuario->LoginName);

        if (!empty($ValidUser)){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT,message:'Ha ocurrido un error inesperado');
        }

        GenericSvc::BeginMultipleOperations(ConnectionEnum::CORE);

        $password       =   md5($NuevoUsuario->Password);

        try{

            $now        = (new DateTime())->format('Y-m-d H:m:s');

            ## EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL USUARIO
            $UsuarioDto     =   new UsuarioDto(
                IdCliente           :   $UsuarioLogin->IdCliente,
                IdEmpresa           :   $UsuarioLogin->IdEmpresa,
                LoginName           :   $NuevoUsuario->LoginName,
                Nombre              :   $NuevoUsuario->Nombre,
                Clave               :   $password,
                IdContacto          :   null,
                Genero              :   null,
                Eliminado           :   0,
                IdPerfil            :   1,
                IdTipoIdioma        :   1,
                FechaCreacion       :   $now,
                FechaUltimaSesion   :   null,
                IdTipoClave         :   2,
                Sigla               :   $NuevoUsuario->Sigla,
                Cargo               :   $NuevoUsuario->Cargo,
                IdTIpoUsuario       :   $NuevoUsuario->IdTipoUsuario,
                IdJefeDirecto       :   $NuevoUsuario->IdUsuario ?: null
            );
            
            ## GUARDAMOS EL NUEVO USUARIO EN LA BBDD
            $UsuarioDto                 =   $UsuarioSvc->Insert($UsuarioDto);

            $LogDto                     =   new LogDto(
                IdTipoLog           :   TipoLogEnum::CREACION_NUEVO_USUARIO,
                IdUsuario           :   $UsuarioLogin->IdUsuario,
                IdUsuarioAfectado   :   $UsuarioDto->IdUsuario,
                Descripcion         :   "El usuario $UsuarioLogin->Nombre ha creado un usuario para $UsuarioDto->LoginName",
                Fecha               :   $now
            ) ;

            $LogSvc->Insert($LogDto);

           

            ## EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
            GenericSvc::SaveMultipleOperations();
            return $UsuarioDto;

        }catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            return null;

        }

    }
}