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

        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Compra"           =>  $CompraDto
        ];

        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Stock')->RenderView('PopupEditarStock',$dataView);
    } 

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionResult]
    public function GuardarNuevoProducto(NuevoUsuarioFilterDto $NuevoCompra) 
    {  

        $CompraBO          =   new CompraBO();

        $CompraDto         =   $CompraBO->GuardarNuevoProducto($NuevoCompra);

        if ( empty($CompraDto) ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT, message:'ha ocurrido un error inesperado');
        }

        return (object)[
            "Fecha_compra"              =>  $CompraDto->Fecha_compra,
            "Descripcion"               =>  $CompraDto->Descripcion,
            "marca"                     =>  $CompraDto->marca,
            "modelo"                    =>  $CompraDto->modelo,
            "Orden_compra"              =>  $CompraDto->Orden_compra,
            "Factura_compra"            =>  $CompraDto->Factura_compra,
            "Precio_U"                  =>  $CompraDto->Precio_U,
            "Cantidad"                  =>  $CompraDto->Cantidad,
            "Precio_total"              =>  $CompraDto->Precio_total,
            "tipo"                      =>  $CompraDto->tipo,
            "Proveedor_idProveedor"     =>  $CompraDto->Proveedor_idProveedor,
            "id_estadoOC"               =>  $CompraDto->id_estadoOC,
            "id_estadoFC"               =>  $CompraDto->id_estadoFC,
            "id_empresa"                =>  $CompraDto->id_empresa,
        ] ;
    } 
   

}