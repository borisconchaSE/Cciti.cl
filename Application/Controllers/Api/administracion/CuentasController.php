<?php

namespace Application\Controllers\Api\administracion;

use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessObjects\Core\UsuarioBO;
use Application\BLL\Filters\CambiarDatosUsuarioFilterDto;
use Application\BLL\Filters\EditarUsuarioFilterDto;
use Application\BLL\Filters\NuevoUsuarioFilterDto;
use Application\BLL\Services\Core\TipoUsuarioSvc;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\Configuration\ConnectionEnum;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Controllers\BaseController; 
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnActionViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;

#[Route(Authorization: 'ENFORCED', AppKey: 'dummie')] 
class CuentasController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory(), forcedController : 'administracion/Cuentas');
    }

    #[Route(Methods: ['POST'], RequireSession:true, Roles:['R_ADMIN_USERS'])]
    #[ReturnActionViewResult]
    public function PopupEditarUsuario(int $IdUsuario) 
    { 

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DEL USUARIO EN EL BO
        $UsuarioDto         =   (new UsuarioBO())->GetUsuario($IdUsuario);

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LOS TIPOS DE USUARIO
        $DatosTipoUsuario   =   (new TipoUsuarioSvc(ConnectionEnum::CORE))->GetAll();
        $ListaUsuario       =   (new UsuarioSvc(ConnectionEnum::CORE))->GetBy(
            [
                new BindVariable('IdUsuario','!=', $UsuarioDto->IdUsuario),
                new BindVariable('IdTipoUsuario','>',1)
            ]
        );
       
        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Usuario"           =>  $UsuarioDto,
            "TipoUsuario"       =>  $DatosTipoUsuario,
            "ListaUsuarios"     =>  $ListaUsuario
        ];

        ## RENDERIZAMOS LA VISTA
        return $this->RenderView('PopupEditarUsuario',$dataView);
    } 


    #[Route(Methods: ['POST'], RequireSession:true, Roles:['R_ADMIN_USERS'])]
    #[ReturnActionResult]
    public function CambiarParametrosUsuario(CambiarDatosUsuarioFilterDto $DatosUsuario) 
    { 
        
        ## ----------------------------------------------------------------------
        ## PARA VALIDAR LA INFORMACIÓN QUE PROVIENE DESDE LA REQUEST
        ## UTILIZAMOS UN TIPO DE DTO LLAMADO FILTER QUE NOS PERMITE IDENTIFICAR 
        ## Y ESTABLECER CUALES SON LOS PARAMETROS QUE ACEPTA LA API
        ## ----------------------------------------------------------------------

        ## en primer lugar validamos los campos ingresados

        ## validamos el id del usuario
        if ($DatosUsuario->IdUsuario < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Usted no puede visualizar este usuario o no tiene permisos sobre el");
        }

        ## validamos el nombre
        if (    strlen($DatosUsuario->Nombre) < 2   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Nombre ingresado invalido");
        }

        ## validamos el nombre
        if (    strlen($DatosUsuario->Cargo) < 2   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Cargo ingresado invalido");
        }

        ## validamos la sigla
        if ( strlen($DatosUsuario->Sigla) != 2 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "La sigla debe contener dos caracteres");
        }

        ## -----------------------------------------------------------------------
        ## UNA VEZ VALIDADO LOS INPUTS
        ## PROCEDEMOS A INSTANCIAR EL BO 
        ## PARA REALIZAR LAS ACCIONES DEL NEGOCIO
        ## -----------------------------------------------------------------------
        $UsuarioBO      =   new UsuarioBO();

        ## PROCEDEMOS A ACTUALIZAR LA INFORMACIÓN EN LA BBDD
        $status         =   $UsuarioBO->UpdateUsuario($DatosUsuario);
        
        if ( $status != true ){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message: "No ha sido posible actualizar el usuario");
        }
        
        return [
            "Nombre"    =>  $DatosUsuario->Nombre,
            "Sigla"     =>  $DatosUsuario->Sigla
        ] ;



    } 
    

    #[Route(Methods: ['POST'], RequireSession:true, Roles:[RolesEnum::R_ADMIN_ENABLE_DISABLE_USERS])]
    #[ReturnActionResult]
    public function ActivarDesactivarUsuario(int $IdUsuario) 
    { 

        ## EN PRIMER LUGAR VALIDAMOS QUE EL ID DEL USUARIO SEA CORRECTO
        if ($IdUsuario < 1){

            ## EN CASO DE QUE SEA MENOR QUE 1 PROCEDEMOS A LANZAR UN ERROR
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Usuario Invalido');

        }


        ## UNA VEZ VALIDADO EL ENDPOINT PROCEDEMOS A REALIZAR LAS ACCIONES SOLICITADAS
        $UsuarioBO      =   new UsuarioBO();


        ## PROCEDEMOS A CAMBIAR EL ESTADO DEL USUARIO ENVIADO
        $NuevoEstado    =   $UsuarioBO->ActivarDesactivarUsuario($IdUsuario);


        ## VALIDAMOS SI EL PROCESO HA SIDO EJECUTADO CORRECTAMENTE
        if ( $NuevoEstado === false ) {

            ## EL ERROR QUE LANZAMOS DEBE SER GENERICO Y NO EXPLICATIVO POR TEMAS DE SEGURIDAD
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Usuario Invalido');
        }


        ## en caso de que todo sea correcto 
        ## procedemos a retornar el nuevo estado
        return [
            "IdUsuario" => $IdUsuario,
            "Estado"    => $NuevoEstado
        ];

        


    }

    #[Route(Methods: ['POST'], RequireSession:true, Roles:['R_ADMIN_USERS'])]
    #[ReturnActionViewResult]
    public function PopupNuevoUsuario() 
    { 

        ## LLAMAMOS LOS DATOS DEL USUARIO QUE INICIO SESIÓN
        $UsuarioDto         =   Session::Instance()->usuario;

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LOS TIPOS DE USUARIO
        $DatosTipoUsuario   =   (new TipoUsuarioSvc(ConnectionEnum::CORE))->GetAll();
        $ListaUsuario       =   (new UsuarioSvc(ConnectionEnum::CORE))->GetBy(
            [
                new BindVariable('IdUsuario','!=', $UsuarioDto->IdUsuario),
                new BindVariable('IdTipoUsuario','>',1)
            ]
        );

        ## GENERAMOS UN ARRAY CON LOS DATOS PARA LA VISTA

        $data   =  (object) [
            "TipoUsuario"       =>  $DatosTipoUsuario,
            "ListaUsuarios"     =>  $ListaUsuario
        ]; 
        ## RENDERIZAMOS LA VISTA
        return $this->RenderView('PopupNuevoUsuario',$data);
    } 

    #[Route(Methods: ['POST'], RequireSession:true, Roles:['R_ADMIN_USERS'])]
    #[ReturnActionResult]
    public function GuardarNuevoUsuario(NuevoUsuarioFilterDto $NuevoUsuario) 
    {  

        $UsuarioBO          =   new UsuarioBO();

        $UsuarioDto         =   $UsuarioBO->CrearNuevoUsuario($NuevoUsuario);

        if ( empty($UsuarioDto) ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT, message:'ha ocurrido un error inesperado');
        }

        return (object)[
            "IdUsuario"     =>  $UsuarioDto->IdUsuario,
            "Nombre"        =>  $UsuarioDto->Nombre,
            "Cargo"         =>  $UsuarioDto->Cargo,
            "LoginName"     =>  $UsuarioDto->LoginName,
            "Sigla"         =>  $UsuarioDto->Sigla,

        ] ;
    } 

    
     

}