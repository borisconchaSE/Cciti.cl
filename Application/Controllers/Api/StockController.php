<?php

namespace Application\Controllers\Api;

use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessObjects\Core\StockBO;
use Application\BLL\Filters\CambiarDatosUsuarioFilterDto;
use Application\BLL\Filters\EditarUsuarioFilterDto;
use Application\BLL\Filters\NuevoUsuarioFilterDto;
use Application\BLL\Services\Core\stockSvc;
use Application\BLL\Services\Core\marcaSvc;
use Application\BLL\Services\Core\empresaSvc;
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
use Intouch\Framework\View\Display;

#[Route(Authorization: 'APP_KEY', AppKey: 'dummie')] 
class StockController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupAgregarStock() 
    {

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN

        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        ## GENERAMOS UN ARRAY CON LOS DATOS PARA LA VISTA

        $data   =  (object) [
            "DatosMarca"        =>  $DatosMarca,
            "DatosEmpresa"      =>  $DatosEmpresa
        ]; 

        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Stock')->RenderView('PopupAgregarStock',$data);
    } 

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function PopupEditarCompra(int $id_stock) 
    { 

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DEL USUARIO EN EL BO
        $StockDto         =   (new StockBO())->GetStock($id_stock);

        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Stock"           =>  $StockDto
        ];

        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Stock')->RenderView('PopupEditarStock',$dataView);
    } 

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionResult]
    public function GuardarStockNuevo($NuevoStock) 
    {  

        $StockBO          =   new StockBO();

        $StockDto         =   $StockBO->GuardarStockNuevo($NuevoStock);

        if ( empty($StockDto) ){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_DATA_INSERT, message:'ha ocurrido un error inesperado');
        }

        return (object)[
            "Fecha_asignacion"      =>  $StockDto->Fecha_asignacion,
            "Descripcion"           =>  $StockDto->Descripcion,
            "Cantidad"              =>  $StockDto->Cantidad,
            "Precio_Unitario"       =>  $StockDto->Precio_Unitario,
            "Precio_total"          =>  $StockDto->Precio_total,
            "IdEmpresa"             =>  $StockDto->IdEmpresa,
            "idMarca"               =>  $StockDto->idMarca,
            "tipo"                  =>  $StockDto->tipo,
            "estado_stock"          =>  $StockDto->estado_stock,
        ] ;
    } 
   

}