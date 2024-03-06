<?php

namespace Application\Controllers\Api;

use Application\BLL\Services\Core\marcaSvc;
use Application\BLL\Services\Core\modeloSvc;
use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessObjects\Core\CompraBO;
use Application\BLL\Filters\CambiarDatosUsuarioFilterDto;
use Application\BLL\Filters\EditarUsuarioFilterDto;
use Application\BLL\Filters\NuevoUsuarioFilterDto;
use Application\BLL\Services\Core\empresaSvc;
use Application\BLL\Services\Core\estadoFCSvc;
use Application\BLL\Services\Core\estadoOCSvc;
use Application\BLL\Services\Core\proveedorSvc;
use Application\Configuration\ConnectionEnum;
use Application\Dao\Entities\Core\estadoOC;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Controllers\BaseController; 
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnActionViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Dao\BindVariable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\View\Display;

#[Route(Authorization: 'APP_KEY', AppKey: 'dummie')] 
class CompraController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupAgregarProducto() 
    {

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN
        
        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        ## GENERAMOS UN ARRAY CON LOS DATOS PARA LA VISTA

        $data   =  (object) [
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC

        ]; 


        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Compra')->RenderView('PopupAgregarProducto',$data);
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupEditarCompra(int $IdO_C) 
    { 

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DEL USUARIO EN EL BO
        $CompraDto         =   (new CompraBO())->GetCompra($IdO_C);

        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Compra"                =>  $CompraDto,
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC
        ];

        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Stock')->RenderView('PopupEditarStock',$dataView);
    } 

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionResult]
    public function GuardarNuevoProducto($NuevoCompra) 
    {  

        $CompraBO          =   new CompraBO();

        $CompraDto         =   $CompraBO->GuardarProducto($NuevoCompra);

        if ( empty($CompraDto) ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT, message:'ha ocurrido un error inesperado');
        }

        return (object)[
            "Fecha_compra"              =>  $CompraDto->Fecha_compra,
            "Descripcion"               =>  $CompraDto->Descripcion,
            "idMarca"                   =>  $CompraDto->idMarca,
            "idModelo"                  =>  $CompraDto->idModelo,
            "Orden_compra"              =>  $CompraDto->Orden_compra,
            "Factura_compra"            =>  $CompraDto->Factura_compra,
            "Precio_U"                  =>  $CompraDto->Precio_U,
            "Cantidad"                  =>  $CompraDto->Cantidad,
            "Precio_total"              =>  $CompraDto->Precio_total,
            "tipo"                      =>  $CompraDto->tipo,
            "idProveedor"               =>  $CompraDto->idProveedor,
            "idEstado_oc"               =>  $CompraDto->idEstado_oc,
            "idEstado_FC"               =>  $CompraDto->idEstado_FC,
            "IdEmpresa"                 =>  $CompraDto->IdEmpresa,
        ] ;
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function CambiarParametrosCompra($DatosCompra) 
    { 
        
        ## ----------------------------------------------------------------------
        ## PARA VALIDAR LA INFORMACIÓN QUE PROVIENE DESDE LA REQUEST
        ## UTILIZAMOS UN TIPO DE DTO LLAMADO FILTER QUE NOS PERMITE IDENTIFICAR 
        ## Y ESTABLECER CUALES SON LOS PARAMETROS QUE ACEPTA LA API
        ## ----------------------------------------------------------------------

        ## en primer lugar validamos los campos ingresados

        ## validamos el id del usuario
        if ($DatosCompra->Cantidad < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Cantidad ingresada invalida");
        }

        if ($DatosCompra->idMarca < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Marca ingresada invalida");
        }

        if ($DatosCompra->idModelo < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Empresa ingresada invalida");
        }

        ## validamos el nombre
        if ( strlen($DatosCompra->Fecha_compra) > 10   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Fecha ingresada invalida");
        }

        ## validamos el nombre
        if ( strlen($DatosCompra->Descripcion) < 4   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Descripcion ingresada invalida");
        }

        ## validamos la sigla
        if ( strlen($DatosCompra->Precio_U) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio unitario ingresado invalida");
        }

        if ( strlen($DatosCompra->Precio_total) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio total ingresado invalida");
        }

        if ( strlen($DatosCompra->tipo) < 5 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Tipo ingresado invalido");
        }

        if ( strlen($DatosCompra->Orden_compra) < 5 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Estado del stock invalido");
        }

        if ( strlen($DatosCompra->Factura_compra) < 5 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Estado del stock invalido");
        }

        ## -----------------------------------------------------------------------
        ## UNA VEZ VALIDADO LOS INPUTS
        ## PROCEDEMOS A INSTANCIAR EL BO 
        ## PARA REALIZAR LAS ACCIONES DEL NEGOCIO
        ## -----------------------------------------------------------------------
        $CompraBO      =   new CompraBO();
        

        ## PROCEDEMOS A ACTUALIZAR LA INFORMACIÓN EN LA BBDD
        $status         =   $CompraBO->UpdateCompra($DatosCompra);

        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();
        
        $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosCompra->idMarca);

        $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosCompra->IdEmpresa);
        
        if ( $status != true ){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message: "No ha sido posible actualizar el producto");
        }
        
        return [
            "Fecha"                 =>  $DatosCompra->Fecha,
            "Descripcion"           =>  $DatosCompra->Descripcion,
            "Cantidad"              =>  $DatosCompra->Cantidad,
            "Precio_Unitario"       =>  $DatosCompra->Precio_Unitario,
            "Precio_total"          =>  $DatosCompra->Precio_total,
            "idMarca"               =>  $Marca->Descripcion,
            "IdEmpresa"             =>  $Empresa->Descripcion,
            "tipo"                  =>  $DatosCompra->tipo,
            "estado_stock"          =>  $DatosCompra->estado_stock
        ] ;



    }
   

}