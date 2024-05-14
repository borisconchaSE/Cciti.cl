<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\stockSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Services\Core\departamentoSvc;
use Application\BLL\Services\Core\empresaSvc;
use Application\BLL\Services\Core\ubicacionSvc;
use Application\BLL\Services\Core\VWEntregadoExcelSvc;
use Application\BLL\Services\Core\VWExcelEntregadoSvc;
use Application\BLL\Services\Core\VWStockExcelSvc;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;

class StockBO 
{

    //FUNCION QUE OBTIENE LOS DATOS A MOSTRAR EN LA TABLA DE STOCK ATUAL
    public function CargarTablaStock() 
    {
        // EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        // PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        // al generar el service debemos especificar la conexión a usar (solo nombre)
   
        $StockSvc           = new VWStockExcelSvc(ConnectionEnum::TI);
        
        // en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        // para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosTabla  =   $StockSvc->GetAll();
            
        // por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosTabla; 
    }

    //FUNCION QUE OBTIENE LOS DATOS A MOSTRAR EN LA TABLA DE STOCK ENTREGADO
    public function CargarTablaEntregado() 
    {
        // EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        // PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        // al generar el service debemos especificar la conexión a usar (solo nombre)
   
        $StockSvc           = new VWEntregadoExcelSvc(ConnectionEnum::TI);
        
        // en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        // para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosTabla  =   $StockSvc->GetAll();
            
        // por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosTabla; 
    }
    
    public function GetStock(int $id_stock){ 
 

        // INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $StockService     =   new stockSvc(ConnectionEnum::TI);

        try{
            // BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $StockService->FindByForeign('id_stock',$id_stock);

        } catch (\Exception $ex) {

            // GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    //FUNCION LA CUAL INYECTA LOS DATOS A GUARDAR DE UN NUEVO PRODUCTO EN LA BBDD
    public function GuardarStockNuevo($NuevoStock) : stockDto|null  {

        // VALIDAMOS LOS PARAMETROS DE LA FECHA
        if (strlen($NuevoStock->Fecha) < 10){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        // VALIDAMOS EL TIPO DE STOCK
        if ( $NuevoStock->tipo > 12){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        // UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        // PROCEDEMOS A GENERAR EL NUEVO ITEM DEL STOCK

        $stockSvc     =   new stockSvc(ConnectionEnum::TI);

        GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);

        try{

            $user   = "Sin Asignar";
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

            // EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL STOCK
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
                estado_stock                :   $NuevoStock->estado_stock,
                idModelo                    :   $NuevoStock->idModelo
            );
            
            // GUARDAMOS EL NUEVO ITEM EN LA BBDD
            $StockDto                 =   $stockSvc->Insert($StockDto);


            // EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
            GenericSvc::SaveMultipleOperations();
            return $StockDto;

        }catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            return null;

        }

    }

    //FUNCION PARA MODIFICAR EL PRODUCTO DEL STOCK(ACTIVO O ENTREGADO)
    public function UpdateStock($DatosStock){

        // INSTANCIAMOS EL SERVICE DEL STOCK
        $StockSvc                   =   new stockSvc(ConnectionEnum::TI);

        // INSTANCIAMOS EL SERVICE DE LOS DEPARTAMENTOS(AREAS) PERTENECIENTES A LA COMPAÑIA
        $DepartamentoSvc            =   new departamentoSvc(ConnectionEnum::TI);

        // INSTANCIAMOS EL SERVICE DE LAS EMPRESAS(SEM, SEMY, ETC.) PERTENECIENTES A LA COMPAÑIA
        $EmpresaSvc                 =   new empresaSvc(ConnectionEnum::TI);

        // INSTANCIAMOS EL SERVICE DE LAS UBICACIONES(PAICAVI, OHIGGINS, ETC) PERTENECIENTES A LA COMPAÑIA
        $UbicacionSvc               =   new ubicacionSvc(ConnectionEnum::TI);

        // BUSCAMOS EL DTO DEL STOCK
        $StockDto         =   $StockSvc->FindByForeign('id_stock',$DatosStock->id_stock);

        // VALIDAMOS SI EL PRODUCTO DEL STOCK EXISTE DENTRO DE LA BBDD
        if ($StockDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El producto no se encuentra disponble');
        }

        // PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{

            //OBTENERMOS EL ESTADO DEL PRODUCTO
            $estado = $DatosStock->estado_stock;

            //VALIDAMOS EL ESTADO DEL PRODUCTO PARA SABER DE QUE VISTA PROVIENE ESTE(ACTUAL O ENTREGADO)
            if($estado == "Entregado"){
        
                // BUSCAMOS LA INFORMACION DEL DEPARTAMENTO
                $DepartamentoDto            =   $DepartamentoSvc->FindByForeign('idDepto',$DatosStock->idDepto);

                // BUSCAMOS LA INFORMACION DE LA EMPRESA
                $EmpresaDto                 =   $EmpresaSvc->FindByForeign('IdEmpresa',$DatosStock->IdEmpresaU);

                // BUSCAMOS LA INFORMACION DE LA UBICACION
                $UbicacionDto               =   $UbicacionSvc->FindByForeign('idubicacion',$DatosStock->idubicacion);
        
                // GUARDAMOS LA INFORMACION OBTENIDA DE LOS DTO EN VARIABLES PARA INSERTARLOS EN LA BBDD
                $FechaA_Data    =   $DatosStock->Fecha_Asignacion;
                $DeptoData      =   $DepartamentoDto->Descripcion;
                $EmpresaU       =   $EmpresaDto->Descripcion;
                $UbicacionData  =   $UbicacionDto->Descripcion;

                //ESTANDARIZAMOS LA FECHA AL FORMATO DE LA BBDD
                $FechaA_Data    =   date("Y-m-d", strtotime($FechaA_Data));
                
                //INYECTAMOS LOS NUEVOS DATOS QUE TENDRA EL PRODUCTO EN EL DTO
                $StockDto->Fecha                            =   $StockDto->Fecha;
                $StockDto->Fecha_asignacion                 =   $FechaA_Data;
                $StockDto->Descripcion                      =   $DatosStock->Descripcion;
                $StockDto->Empresa_asignado                 =   $EmpresaU;
                $StockDto->Departamento                     =   $DeptoData;
                $StockDto->Ubicacion                        =   $UbicacionData;
                $StockDto->Cantidad                         =   $DatosStock->Cantidad;
                $StockDto->Precio_Unitario                  =   $DatosStock->Precio_Unitario;
                $StockDto->Precio_total                     =   $StockDto->Precio_total;
                $StockDto->estado_stock                     =   $DatosStock->estado_stock;
                $StockDto->tipo                             =   $DatosStock->tipo;
                $StockDto->idMarca                          =   $DatosStock->idMarca;
                $StockDto->IdEmpresa                        =   $DatosStock->IdEmpresa;
                $StockDto->idModelo                         =   $DatosStock->idModelo;
                $StockDto->idCentro                         =   $DatosStock->idCentro;

                // ACTUALIZAMOS LOS VALOREDE DE LA BBDD
                $StockSvc->Update($StockDto);

                return true;

            }elseif($estado == "En Stock"){

                //ESTANDARIZAMOS LA FECHA AL FORMATO DE LA BBDD
                $FechaData      =   $DatosStock->Fecha;
                $FechaData      =   date("Y-m-d", strtotime($FechaData));
                
                //INYECTAMOS LOS NUEVOS DATOS QUE TENDRA EL PRODUCTO EN EL DTO
                $StockDto->Fecha                            =   $FechaData;
                $StockDto->Descripcion                      =   $DatosStock->Descripcion;
                $StockDto->Cantidad                         =   $DatosStock->Cantidad;
                $StockDto->Precio_Unitario                  =   $DatosStock->Precio_Unitario;
                $StockDto->Precio_total                     =   $DatosStock->Precio_total;
                $StockDto->idMarca                          =   $DatosStock->idMarca;
                $StockDto->idModelo                         =   $DatosStock->idModelo;
                $StockDto->IdEmpresa                        =   $DatosStock->IdEmpresa;
                $StockDto->tipo                             =   $DatosStock->tipo;
                $StockDto->estado_stock                     =   $DatosStock->estado_stock;
                
                // ACTUALIZAMOS LOS VALORES EN LA BBDD
                $StockSvc->Update($StockDto);
    
                return true;

            }


        } catch (\Exception $ex) {

            return false;

        }

    }


}