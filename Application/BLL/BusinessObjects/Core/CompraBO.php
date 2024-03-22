<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\marcaSvc;
use Application\BLL\Services\Core\modeloSvc;
use Application\BLL\Services\Core\ordencompraSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\BLL\DataTransferObjects\Core\ordencompraDto;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Application\BLL\Services\Core\stockSvc;
use Application\BLL\DataTransferObjects\Core\stockDto; 

class CompraBO 
{

    public function CargarTablaCompra() 
    {
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        ## al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new ordencompraSvc(ConnectionEnum::TI);

        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->BuscarCompras();

                    
        ## por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    public function CargarTablaGeneralCompras() 
    {
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        ## al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new ordencompraSvc(ConnectionEnum::TI);

        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->BuscarComprasGenerales();

                    
        ## por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    public function CargarTablaGastos() 
    {
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        ## al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new ordencompraSvc(ConnectionEnum::TI);

        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->BuscarGastos();

                    
        ## por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    public function GetCompra(int $IdO_C){ 
 

        ## INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $CompraService     =   new ordencompraSvc(ConnectionEnum::TI);

        try{
            ## BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $CompraService->FindByForeign('IdO_C',$IdO_C);

        } catch (\Exception $ex) {

            ## GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    public function GuardarProducto($NuevoCompra) : ordencompraDto|null  {

        ## VALIDAMOS LOS PARAMETROS DE LA FECHA
        if (strlen($NuevoCompra->Fecha_compra) < 10){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS EL TIPO DE STOCK
        if ( $NuevoCompra->tipo > 1){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        ## PROCEDEMOS A GENERAR EL NUEVO ITEM DEL STOCK

        $ordenSvc     =   new ordencompraSvc(ConnectionEnum::TI);
        $StockSvc     =   new stockSvc(ConnectionEnum::TI);

        $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$NuevoCompra->idMarca);

        $Modelo        =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$NuevoCompra->idModelo);

        GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);

        try{
            $user           = "Sin Asignar";
            $estado_stock   = "En Stock";
            $ciclo          = 1;
            $limite         = $NuevoCompra->Cantidad;
            $tipoproducto   = 1;

            if ($NuevoCompra->tipo == 0){
                $Tipo = "Original";
            }else{
                $Tipo = "Alternativo";
            };


            ## EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL PRODUCTO
            $OrdenDto     =   new ordencompraDto(
                Fecha_compra                :   $NuevoCompra->Fecha_compra,
                Descripcion                 :   $NuevoCompra->Descripcion,
                marca                       :   $Marca->Descripcion,
                modelo                      :   $Modelo->Descripcion,
                Orden_compra                :   $NuevoCompra->Orden_compra,
                Factura_compra              :   $NuevoCompra->Factura_compra,
                Precio_U                    :   $NuevoCompra->Precio_U,
                Cantidad                    :   $NuevoCompra->Cantidad,
                Precio_total                :   $NuevoCompra->Precio_total,
                tipo                        :   $Tipo,
                idProveedor                 :   $NuevoCompra->idProveedor,
                idEstado_oc                 :   $NuevoCompra->idEstado_oc,
                idEstado_FC                 :   $NuevoCompra->idEstado_FC,
                IdEmpresa                   :   $NuevoCompra->IdEmpresa,
                idTipoProducto              :   $tipoproducto
            );

            
            ## GUARDAMOS EL NUEVO ITEM EN LA BBDD
            $OrdenDto                   =   $ordenSvc->Insert($OrdenDto);

            ## CICLO PARA INSERTAR EN EL STOCK ACTUAL
            if ($limite == 1){
                ## PROCEDEMOS A CREAR EL DTO PARA INSERTAR AL STOCK ACTUAL
                $StockDto     =   new stockDto(
                    Fecha                       :   $NuevoCompra->Fecha_compra,
                    Descripcion                 :   $NuevoCompra->Descripcion,
                    Empresa_asignado            :   $user,
                    Departamento                :   $user,
                    Ubicacion                   :   $user,
                    Cantidad                    :   $ciclo,
                    Precio_Unitario             :   $NuevoCompra->Precio_U,
                    Precio_total                :   $NuevoCompra->Precio_total,
                    IdEmpresa                   :   $NuevoCompra->IdEmpresa,
                    idMarca                     :   $NuevoCompra->idMarca,
                    tipo                        :   $Tipo,
                    estado_stock                :   $estado_stock
                );

                $StockDto                   =   $StockSvc->Insert($StockDto);
            }else{

                $StockDto     =   new stockDto(
                    Fecha                       :   $NuevoCompra->Fecha_compra,
                    Descripcion                 :   $NuevoCompra->Descripcion,
                    Empresa_asignado            :   $user,
                    Departamento                :   $user,
                    Ubicacion                   :   $user,
                    Cantidad                    :   $ciclo,
                    Precio_Unitario             :   $NuevoCompra->Precio_U,
                    Precio_total                :   $NuevoCompra->Precio_U,
                    IdEmpresa                   :   $NuevoCompra->IdEmpresa,
                    idMarca                     :   $NuevoCompra->idMarca,
                    tipo                        :   $Tipo,
                    estado_stock                :   $estado_stock
                );

                while($ciclo <= $limite){
                    $StockDto                   =   $StockSvc->Insert($StockDto);
                    $ciclo  = $ciclo+1;
                };
            }


            ## EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
            GenericSvc::SaveMultipleOperations();
            return $OrdenDto;

        }catch (\Exception $ex) {
            GenericSvc::UndoMultipleOperations();
            return null;

        }

    }

    public function UpdateCompra($DatosCompra){

        ## INSTANCIAMOS EL SERVICE DEL USUARIO
        $CompraSvc      = new ordencompraSvc(ConnectionEnum::TI);

        ## BUSCAMOS EL DTO DEL USUARIO
        $CompraDto         =   $CompraSvc->FindByForeign('idO_C',$DatosCompra->idO_C);

        ## VALIDAMOS SI EL USUARIO EXISTEE DENTRO DE LA BBDD
        if ($CompraDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El producto no se encuentra disponble');
        }

        ## PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{
            $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosCompra->idMarca);
            $Modelo         =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$DatosCompra->idModelo);

            $FechaData      =   $DatosCompra->Fecha_compra;
            $FechaData      =   date("Y-m-d", strtotime($FechaData));
            ## 
            $CompraDto->Fecha_compra                    =   $FechaData;
            $CompraDto->Descripcion                     =   $DatosCompra->Descripcion;
            $CompraDto->Orden_compra                    =   $DatosCompra->Orden_compra;
            $CompraDto->Factura_compra                  =   $DatosCompra->Factura_compra;
            $CompraDto->Cantidad                        =   $DatosCompra->Cantidad;
            $CompraDto->Precio_U                        =   $DatosCompra->Precio_U;
            $CompraDto->Precio_total                    =   $DatosCompra->Precio_total;
            $CompraDto->marca                           =   $Marca->Descripcion;
            $CompraDto->modelo                          =   $Modelo->Descripcion;
            $CompraDto->IdEmpresa                       =   $DatosCompra->IdEmpresa;
            $CompraDto->tipo                            =   $DatosCompra->tipo;
            $CompraDto->idProveedor                     =   $DatosCompra->idProveedor;
            $CompraDto->idEstado_oc                     =   $DatosCompra->idEstado_oc;
            $CompraDto->idEstado_FC                     =   $DatosCompra->idEstado_FC;

            ## ACTUALIZAMOS LOS VALORES EN LA BBDD
            $CompraSvc->Update($CompraDto);

            return true;


        } catch (\Exception $ex) {

            return false;

        }

    }


}