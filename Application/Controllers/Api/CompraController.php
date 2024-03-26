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
use Application\BLL\Services\Core\tipoproductoSvc;
use Application\Configuration\ConnectionEnum;
use Application\Dao\Entities\Core\estadoOC;
use Application\Dao\Entities\Core\modelo;
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
    public function PopupAgregarProducto($identificador) 
    {

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN
        
        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        $DatosTipo          =   (new tipoproductoSvc(ConnectionEnum::TI))->GetAll();

        ## GENERAMOS UN ARRAY CON LOS DATOS PARA LA VISTA

        $data   =  (object) [
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC,
            "DatosTipo"             =>  $DatosTipo

        ]; 

        if($identificador == 'Agregar Tonner'){

            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Compra')->RenderView('PopupAgregarProducto',$data);

        }elseif($identificador == 'Agregar Activo'){
            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Generales')->RenderView('PopupAgregarGenerales',$data);

        }else{
            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Gastos')->RenderView('PopupAgregarGastos',$data);

        }     
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupEditarCompra(int $IdO_C) 
    { 

        $ListasGastos = [4,7];

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN EN EL BO
        $CompraDto          =   (new CompraBO())->GetCompra($IdO_C);

        $Marca              =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('Descripcion',$CompraDto->marca);

        $Modelo             =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('Descripcion',$CompraDto->modelo);

        $Proveedor          =   (new proveedorSvc(ConnectionEnum::TI))->GetBy(new BindVariable('idProveedor','=',$CompraDto->idProveedor));

        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosModelo        =   (new modeloSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        $DatosProveedor     =   (new proveedorSvc(ConnectionEnum::TI))->GetAll();

        $DatosOC            =   (new estadoOCSvc(ConnectionEnum::TI))->GetAll();

        $DatosFC            =   (new estadoFCSvc(ConnectionEnum::TI))->GetAll();

        $DatosTipo          =   (new tipoproductoSvc(ConnectionEnum::TI))->GetAll();





        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Compra"                =>  $CompraDto,
            "Marca"                 =>  $Marca,
            "Modelo"                =>  $Modelo,
            "DatosMarca"            =>  $DatosMarca,
            "DatosModelo"           =>  $DatosModelo,
            "DatosEmpresa"          =>  $DatosEmpresa,
            "DatosProveedor"        =>  $DatosProveedor,
            "DatosOC"               =>  $DatosOC,
            "DatosFC"               =>  $DatosFC,
            "DatosTipo"             =>  $DatosTipo,
            "Proveedor"             =>  $Proveedor,
        ];

        if(in_array($CompraDto->idTipoProducto,$ListasGastos)){
            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Gastos')->RenderView('PopupEditarGastos',$dataView);
        }elseif($CompraDto->idTipoProducto == 1){
            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Compra')->RenderView('PopupEditarCompra',$dataView);
        }else{   
            ## RENDERIZAMOS LA VISTA
            return Display::GetRenderer('Core/Generales')->RenderView('PopupEditarGenerales',$dataView);
        }
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
        
        ## en primer lugar validamos los campos ingresados

        ## validamos el nombre
        if ( strlen($DatosCompra->Fecha_compra) > 10   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Fecha ingresada invalida");
        }

        ## validamos el nombre
        if ( strlen($DatosCompra->Descripcion) < 1   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Descripcion ingresada invalida");
        }

        if ( strlen($DatosCompra->Precio_total) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio total ingresado invalida");
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
        
        $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosCompra->idMarca);
        $Modelo         =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$DatosCompra->idModelo);
        $Proveedor      =   (new proveedorSvc(ConnectionEnum::TI))->FindByForeign('idProveedor',$DatosCompra->idProveedor);
        $Estado_oc      =   (new estadoOCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_oc',$DatosCompra->idEstado_oc);
        $Estado_FC      =   (new estadoFCSvc(ConnectionEnum::TI))->FindByForeign('idEstado_FC',$DatosCompra->idEstado_FC);
        $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosCompra->IdEmpresa);
        
        if ( $status != true ){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message: "No ha sido posible actualizar el producto");
        }
        
        return [
            "Fecha_compra"                  =>  $DatosCompra->Fecha_compra,
            "Descripcion"                   =>  $DatosCompra->Descripcion,
            "marca"                         =>  $Marca->Descripcion,
            "modelo"                        =>  $Modelo->Descripcion,
            "Orden_compra"                  =>  $DatosCompra->Orden_compra,
            "Factura_compra"                =>  $DatosCompra->Factura_compra,
            "Precio_U"                      =>  $DatosCompra->Precio_U,
            "Cantidad"                      =>  $DatosCompra->Cantidad,
            "Precio_total"                  =>  $DatosCompra->Precio_total,
            "tipo"                          =>  $DatosCompra->tipo,
            "idProveedor"                   =>  $Proveedor->Descripcion,
            "idEstado_oc"                   =>  $Estado_oc->Descripcion,
            "idEstado_FC"                   =>  $Estado_FC->Descripcion,
            "IdEmpresa"                     =>  $Empresa->Descripcion
        ] ;



    }
   

}