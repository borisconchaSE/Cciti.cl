<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\stockSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\BLL\BusinessEnumerations\TipoLogEnum;
use Application\BLL\DataTransferObjects\Core\LogDto;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Filters\NuevoUsuarioFilterDto;
use Application\BLL\Services\Core\LogSvc;
use Application\BLL\Services\Core\UsuarioSvc;
use DateTime;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;

class StockBO 
{

    public function CargarTablaStock() 
    {
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        ## al generar el service debemos especificar la conexión a usar (solo nombre)
   
        $StockSvc           = new stockSvc(ConnectionEnum::TI);
        
        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosTabla  =   $StockSvc->BuscarStock();
            
        ## por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosTabla; 
    }
    
    public function GetStock(int $id_stock){ 
 

        ## INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $StockService     =   new stockSvc(ConnectionEnum::TI);

        try{
            ## BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $StockService->FindByForeign('id_stock',$id_stock);

        } catch (\Exception $ex) {

            ## GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    public function GuardarStockNuevo($NuevoStock) : stockDto|null  {

        ## VALIDAMOS LOS PARAMETROS DE LA FECHA
        if (strlen($NuevoStock->Fecha_asignacion) < 11){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS EL TIPO DE STOCK
        if ( $NuevoStock->tipo < 12){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        ## PROCEDEMOS A GENERAR EL NUEVO ITEM DEL STOCK

        $stockSvc     =   new stockSvc(ConnectionEnum::TI);

        GenericSvc::BeginMultipleOperations(ConnectionEnum::CORE);

        try{


            ## EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL STOCK
            $StockDto     =   new stockDto(
                Fecha_asignacion            :   $NuevoStock->Fecha_asignacion,
                Descripcion                 :   $NuevoStock->Descripcion,
                Cantidad                    :   $NuevoStock->Cantidad,
                Precio_Unitario             :   $NuevoStock->Precio_Unitario,
                Precio_total                :   $NuevoStock->Precio_total,
                IdEmpresa                   :   $NuevoStock->IdEmpresa,
                idMarca                     :   $NuevoStock->idMarca,
                tipo                        :   $NuevoStock->tipo,
                estado_stock                :   $NuevoStock->estado_stock
            );
            
            ## GUARDAMOS EL NUEVO ITEM EN LA BBDD
            $StockDto                 =   $stockSvc->Insert($StockDto);


            ## EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
            GenericSvc::SaveMultipleOperations();
            return $StockDto;

        }catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            return null;

        }

    }


}