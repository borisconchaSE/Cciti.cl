<?php

namespace Application\Controllers\Api;

use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessObjects\Listas\TableCacheBO;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Services\Core\stockSvc;
use Application\BLL\Services\Core\VWActivosExcelSvc;
use Application\BLL\Services\Core\VWCompraExcelSvc;
use Application\BLL\Services\Core\VWExcelEntregadoSvc;
use Application\BLL\Services\Core\VWGastosExcelSvc;
use Application\BLL\Services\Core\VWStockExcelSvc;
use Application\BLL\Services\Listas\ListaClientesSvc;
use Application\Configuration\ConnectionEnum;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Controllers\BaseController; 
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnCacheTableData;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Environment\RedisDataTable;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Environment\SessionRedis;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

#[Route(Authorization: 'ALLOW_ALL' , AppKey: 'dummie')] 
class TableController extends BaseController
{
    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }

    #[Route(Methods: ['GET','POST'], RequireSession:true)]
    #[ReturnCacheTableData]
    public function getdata($guid) 
    {
 
        
        
        $RequestData    =   (object)$_REQUEST;
        $IdUsuario      =   Session::Instance()->usuario->IdUsuario;
        $guid           =   "RPHPDATATABLE_" .$IdUsuario . "_" .$guid;


        ## VALIDAMOS SI EL INPUT ES CORRECTO
        $datos      =   TableCacheBO::GetAll($guid);

        if (empty($datos)){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
        }
        

 

        return  TableCacheBO::GetForDataTable(
            guid            :   $guid,
            startIdx        :   $RequestData->start,
            SearchCriterio  :   trim($RequestData->search['value']),
            draw            :   $RequestData->draw,
            length          :   $RequestData->length
        ); 

        
         
    }
    #[Route(Methods: ['GET','POST'], RequireSession:true)]
    #[ReturnCacheTableData]
    public function lista(string $guid, string $filename) : void
    {

        $IdUsuario      =   Session::Instance()->usuario->IdUsuario;
        $fecha_hoy  = date("d-m-Y");
        if($guid == "tbListadoStock"){

            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $VistaSvc       =   new VWStockExcelSvc(ConnectionEnum::TI);
            $datos           =   $VistaSvc->GetAll();

            $cantidad = count((array)$datos->Values);

            for ($i = 0; $i < ($cantidad); $i++) {
                settype($datos->Values[$i], "array");
            }


            if (empty($datos)){
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
            }

            ## UNA VEZ QUE OBTENEMOS LOS DATOS, PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LA LISTA
            try{
                $filename   =   "Stock_Actual_".$fecha_hoy;

            }catch (\Exception $ex) {

            }

            $filename   =   $filename . ".xlsx";

        
            $ColumnList     =   array_keys($datos->Values[0]);

            $replacements = array(0 => 'Fecha Llegada', 1 => 'Nombre Producto', 3 => 'Precio Unitario',
            8 => 'Estado producto', 7 => 'Tipo tonner');
            $final_array = array_replace($ColumnList, $replacements);
    
        }elseif($guid == "tbListadoEntregado"){

            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $VistaSvc           =   new VWExcelEntregadoSvc(ConnectionEnum::TI);
            $datos              =  $VistaSvc->GetAll();

            $cantidad = count((array)$datos->Values);

            for ($i = 0; $i < ($cantidad); $i++) {
                settype($datos->Values[$i], "array");
            }


            if (empty($datos)){
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
            }

            ## UNA VEZ QUE OBTENEMOS LOS DATOS, PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LA LISTA
            try{
                $filename   =   "Stock_Entregado_".$fecha_hoy;

            }catch (\Exception $ex) {

            }

            $filename   =   $filename . ".xlsx";

        
            $ColumnList     =   array_keys($datos->Values[0]);

            $replacements = array(0 => 'Fecha Entrega', 1 => 'Nombre Producto', 3 => 'Precio Producto', 6 => 'Empresa producto',
            7 => 'Empresa asignado', 10 => 'Tipo tonner');
            $final_array = array_replace($ColumnList, $replacements);

        }elseif($guid == "tbListadoCompra"){

            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $VistaSvc           =   new VWCompraExcelSvc(ConnectionEnum::TI);
            $datos              =  $VistaSvc->GetAll();

            $cantidad = count((array)$datos->Values);

            for ($i = 0; $i < ($cantidad); $i++) {
                settype($datos->Values[$i], "array");
            }


            if (empty($datos)){
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
            }

            ## UNA VEZ QUE OBTENEMOS LOS DATOS, PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LA LISTA
            try{
                $filename   =   "Compras_Tonners_".$fecha_hoy;

            }catch (\Exception $ex) {

            }

            $filename   =   $filename . ".xlsx";

        
            $ColumnList     =   array_keys($datos->Values[0]);

            $replacements = array(0 => 'Fecha Compra', 1 => 'Nombre Producto', 4 => 'Orden Compra', 
            5 => 'Factura Compra', 6 => 'Precio Unitario', 8 => 'Precio Total', 11 => 'Estado OC',
            12 => 'Estado FC');
            $final_array = array_replace($ColumnList, $replacements);

        }elseif($guid == "tbListadoActivos"){

            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $VistaSvc           =   new VWActivosExcelSvc(ConnectionEnum::TI);
            $datos              =   $VistaSvc->GetAll();

            $cantidad = count((array)$datos->Values); 

            for ($i = 0; $i < ($cantidad); $i++) {
                settype($datos->Values[$i], "array");
            }

            if (empty($datos)){
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
            }

            ## UNA VEZ QUE OBTENEMOS LOS DATOS, PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LA LISTA
            try{
                $filename   =   "Compras_Activos_".$fecha_hoy;

            }catch (\Exception $ex) { 

            }

            $filename   =   $filename . ".xlsx";    
        
            $ColumnList     =   array_keys($datos->Values[0]);

            $replacements = array(0 => 'Fecha Compra', 2 => 'Rut Proveedor', 4 => 'Nombre Producto', 
            5 => 'Orden Compra', 6 => 'Factura Compra', 8 => 'Precio Total', 11 => 'Estado Activo', 
            11 => 'Estado OC', 12 => 'Estado FC');

            $final_array = array_replace($ColumnList, $replacements);

        }elseif($guid == "tbListadoGastos"){

            ## VALIDAMOS SI EL INPUT ES CORRECTO
            $VistaSvc           =   new VWGastosExcelSvc(ConnectionEnum::TI);
            $datos              =   $VistaSvc->GetAll();

            $cantidad = count((array)$datos->Values); 

            for ($i = 0; $i < ($cantidad); $i++) {
                settype($datos->Values[$i], "array");
            }

            if (empty($datos)){
                throw new BusinessException(code: ExceptionCodesEnum::ERR_DATA_READ,message:'INFORMACIÓN NO DISPONIBLE');
            }

            ## UNA VEZ QUE OBTENEMOS LOS DATOS, PROCEDEMOS A BUSCAR LA INFORMACIÓN DE LA LISTA
            try{
                $filename   =   "Gastos_".$fecha_hoy;

            }catch (\Exception $ex) { 

            }

            $filename   =   $filename . ".xlsx";    
        
            $ColumnList     =   array_keys($datos->Values[0]);

            $replacements = array(0 => 'Fecha Compra', 1 => 'Nombre Producto', 2 => 'Orden Compra',
            3 => 'Factura Compra', 4 => 'Precio Total', 7 => 'Estado OC', 8 => 'Estado FC');

            $final_array = array_replace($ColumnList, $replacements);

        }
    
        $data   = [
            $final_array
        ];

        $pure_data    =   array_map(function($x){

            if (in_array("NombreCliente",$x)){
                $x['NombreCliente'] = ucwords($x['NombreCliente']);
            }

            return array_values((array)$x);

        },$datos->Values) ;

        unset($datos);

        $data   =   array_merge($data,$pure_data);

 
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();

     
        $spreadsheet->getActiveSheet()->fromArray($data, null, 'A1');

        // Create a writer and save the spreadsheet as an XLSX file
     
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit();

 
  

        
    }

    
 
  
}