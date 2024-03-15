<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\stockSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Services\Core\departamentoSvc;
use Application\BLL\Services\Core\empresaSvc;
use Application\BLL\Services\Core\ubicacionSvc;
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

    public function CargarTablaEntregado() 
    {
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        ## al generar el service debemos especificar la conexión a usar (solo nombre)
   
        $StockSvc           = new stockSvc(ConnectionEnum::TI);
        
        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosTabla  =   $StockSvc->BuscarEntregado();
            
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
        if (strlen($NuevoStock->Fecha) < 10){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS EL TIPO DE STOCK
        if ( $NuevoStock->tipo > 12){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        ## PROCEDEMOS A GENERAR EL NUEVO ITEM DEL STOCK

        $stockSvc     =   new stockSvc(ConnectionEnum::TI);

        GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);

        try{

            $user   = "No aplica";
            if ($NuevoStock->Fecha_Asignacion == ""){
                $Fecha  = null;
            }else{
                $Fecha  = $NuevoStock->Fecha_Asignacion;
            }
            
            if ($NuevoStock->estado_stock == 0){
                $NuevoStock->estado_stock = "En Stock";
            }else{
                $NuevoStock->estado_stock = "Entregado";
            };

            if ($NuevoStock->tipo == 0){
                $NuevoStock->tipo = "Original";
            }else{
                $NuevoStock->tipo = "Alternativo";
            };

            ## EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL STOCK
            $StockDto     =   new stockDto(
                Fecha                       :   $NuevoStock->Fecha,
                Fecha_asignacion            :   $Fecha,
                Descripcion                 :   $NuevoStock->Descripcion,
                Empresa_asignado            :   $user,
                Departamento                :   $user,
                Ubicacion                   :   $user,
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

    public function UpdateStock($DatosStock){

        ## INSTANCIAMOS EL SERVICE DEL USUARIO
        $StockSvc                   =   new stockSvc(ConnectionEnum::TI);
        $DepartamentoSvc            =   new departamentoSvc(ConnectionEnum::TI);
        $EmpresaSvc                 =   new empresaSvc(ConnectionEnum::TI);
        $UbicacionSvc               =   new ubicacionSvc(ConnectionEnum::TI);

        ## BUSCAMOS EL DTO DEL USUARIO
        $StockDto         =   $StockSvc->FindByForeign('id_stock',$DatosStock->id_stock);

        ## VALIDAMOS SI EL USUARIO EXISTEE DENTRO DE LA BBDD
        if ($StockDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El producto no se encuentra disponble');
        }

        ## PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{

            $estado = $DatosStock->estado_stock;

            if($estado == "Entregado"){
        
                ## BUSCAMOS EL DTO DEL USUARIO
                $StockDto                   =   $StockSvc->FindByForeign('id_stock',$DatosStock->id_stock);
                $DepartamentoDto            =   $DepartamentoSvc->FindByForeign('idDepto',$DatosStock->idDepto);
                $EmpresaDto                 =   $EmpresaSvc->FindByForeign('IdEmpresa',$DatosStock->IdEmpresaU);
                $UbicacionDto               =   $UbicacionSvc->FindByForeign('idubicacion',$DatosStock->idubicacion);
        
                $FechaData      =   $DatosStock->Fecha;
                $DeptoData      =   $DepartamentoDto->Descripcion;
                $EmpresaU       =   $EmpresaDto->Descripcion;
                $UbicacionData  =   $UbicacionDto->Descripcion;
                $FechaData      =   date("Y-m-d", strtotime($FechaData));
                ## 
                $StockDto->Fecha                            =   $FechaData;
                $StockDto->Descripcion                      =   $DatosStock->Descripcion;
                $StockDto->Cantidad                         =   $DatosStock->Cantidad;
                $StockDto->Precio_Unitario                  =   $DatosStock->Precio_Unitario;
                $StockDto->Precio_total                     =   $DatosStock->Precio_total;
                $StockDto->Empresa_asignado                 =   $EmpresaU;
                $StockDto->Departamento                     =   $DeptoData;
                $StockDto->Ubicacion                        =   $UbicacionData;
                $StockDto->idMarca                          =   $DatosStock->idMarca;
                $StockDto->IdEmpresa                        =   $DatosStock->IdEmpresa;
                $StockDto->tipo                             =   $DatosStock->tipo;
                $StockDto->estado_stock                     =   $DatosStock->estado_stock;

            }elseif($estado == "En Stock"){

                $FechaData      =   $DatosStock->Fecha;
                $FechaData      =   date("Y-m-d", strtotime($FechaData));
                ## 
                $StockDto->Fecha                            =   $FechaData;
                $StockDto->Descripcion                      =   $DatosStock->Descripcion;
                $StockDto->Cantidad                         =   $DatosStock->Cantidad;
                $StockDto->Precio_Unitario                  =   $DatosStock->Precio_Unitario;
                $StockDto->Precio_total                     =   $DatosStock->Precio_total;
                $StockDto->idMarca                          =   $DatosStock->idMarca;
                $StockDto->IdEmpresa                        =   $DatosStock->IdEmpresa;
                $StockDto->tipo                             =   $DatosStock->tipo;
                $StockDto->estado_stock                     =   $DatosStock->estado_stock;
                
                ## ACTUALIZAMOS LOS VALORES EN LA BBDD
                $StockSvc->Update($StockDto);
    
                return true;

            }


        } catch (\Exception $ex) {

            return false;

        }

    }


}