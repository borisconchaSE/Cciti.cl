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
    public function PopupEditarStock(int $id_stock) 
    { 

        ## PROCEDEMOS A BUSCAR LA INFORMACIÓN DEL USUARIO EN EL BO
        $StockDto           =   (new StockBO())->GetStock($id_stock);
        $DatosMarca         =   (new marcaSvc(ConnectionEnum::TI))->GetAll();

        $DatosEmpresa       =   (new empresaSvc(ConnectionEnum::TI))->GetAll();

        ## GUARDAMOS LOS DATOS EN UN ARREGLO PARA ENVIARSELOS A LA VISTA
        $dataView           =  (object) [
            "Stock"                     =>  $StockDto,
            "DatosMarca"                =>  $DatosMarca,
            "DatosEmpresa"              =>  $DatosEmpresa
        ];

        ## RENDERIZAMOS LA VISTA
        return Display::GetRenderer('Core/Stock')->RenderView('PopupEditarStock',$dataView);
    }

    #[Route(Methods: ['POST'], RequireSession:true)]
    #[ReturnActionViewResult]
    public function CambiarParametrosStock($DatosStock) 
    { 
        
        ## ----------------------------------------------------------------------
        ## PARA VALIDAR LA INFORMACIÓN QUE PROVIENE DESDE LA REQUEST
        ## UTILIZAMOS UN TIPO DE DTO LLAMADO FILTER QUE NOS PERMITE IDENTIFICAR 
        ## Y ESTABLECER CUALES SON LOS PARAMETROS QUE ACEPTA LA API
        ## ----------------------------------------------------------------------

        ## en primer lugar validamos los campos ingresados

        ## validamos el id del usuario
        if ($DatosStock->Cantidad < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Cantidad ingresada invalida");
        }

        if ($DatosStock->idMarca < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Marca ingresada invalida");
        }

        if ($DatosStock->IdEmpresa < 1 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Empresa ingresada invalida");
        }

        ## validamos el nombre
        if ( strlen($DatosStock->Fecha) > 10   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Fecha ingresada invalida");
        }

        ## validamos el nombre
        if ( strlen($DatosStock->Descripcion) < 4   ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Descripcion ingresada invalida");
        }

        ## validamos la sigla
        if ( strlen($DatosStock->Precio_Unitario) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio unitario ingresado invalida");
        }

        if ( strlen($DatosStock->Precio_total) < 4 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Precio total ingresado invalida");
        }

        if ( strlen($DatosStock->tipo) < 5 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Tipo ingresado invalido");
        }

        if ( strlen($DatosStock->estado_stock) < 5 ) {
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: "Estado del stock invalido");
        }

        ## -----------------------------------------------------------------------
        ## UNA VEZ VALIDADO LOS INPUTS
        ## PROCEDEMOS A INSTANCIAR EL BO 
        ## PARA REALIZAR LAS ACCIONES DEL NEGOCIO
        ## -----------------------------------------------------------------------
        $StockBO      =   new StockBO();
        

        ## PROCEDEMOS A ACTUALIZAR LA INFORMACIÓN EN LA BBDD
        $status         =   $StockBO->UpdateStock($DatosStock);
        $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosStock->idMarca);

        $Empresa        =   (new empresaSvc(ConnectionEnum::TI))->FindByForeign('IdEmpresa',$DatosStock->IdEmpresa);
        
        if ( $status != true ){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_UPDATE, message: "No ha sido posible actualizar el producto");
        }
        
        return [
            "Fecha"                 =>  $DatosStock->Fecha,
            "Descripcion"           =>  $DatosStock->Descripcion,
            "Cantidad"              =>  $DatosStock->Cantidad,
            "Precio_Unitario"       =>  $DatosStock->Precio_Unitario,
            "Precio_total"          =>  $DatosStock->Precio_total,
            "idMarca"               =>  $Marca->Descripcion,
            "IdEmpresa"             =>  $Empresa->Descripcion,
            "tipo"                  =>  $DatosStock->tipo,
            "estado_stock"          =>  $DatosStock->estado_stock
        ] ;



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
            "Fecha"                 =>  $StockDto->Fecha,
            "Fecha_Asignacion"      =>  $StockDto->Fecha_Asignacion,
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