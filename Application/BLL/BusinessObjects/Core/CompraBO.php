<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\ordenCompraSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\BLL\DataTransferObjects\Core\ordenCompraDto;
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

        $CompraSvc      = new ordenCompraSvc(ConnectionEnum::TI);

        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->BuscarCompras();

                    
        ## por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    public function GetCompra(int $IdO_C){ 
 

        ## INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $CompraService     =   new ordenCompraSvc(ConnectionEnum::TI);

        try{
            ## BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $CompraService->FindByForeign('IdO_C',$IdO_C);

        } catch (\Exception $ex) {

            ## GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    public function GuardarProducto($NuevoCompra) : ordenCompraDto|null  {

        ## VALIDAMOS LOS PARAMETROS DE LA FECHA
        if (strlen($NuevoCompra->Fecha_compra) < 11){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## VALIDAMOS EL TIPO DE STOCK
        if ( $NuevoCompra->tipo < 12){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        ## UNA VEZ VALIDADOS LOS DATOS ENVIAMOS A LA FUNCIÓN
        ## PROCEDEMOS A GENERAR EL NUEVO ITEM DEL STOCK

        $ordenSvc     =   new ordenCompraSvc(ConnectionEnum::TI);
        $StockSvc     =   new stockSvc(ConnectionEnum::TI);

        GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);

        try{
            $user           = "No aplica";
            $estado_stock   = "En Stock";
            $ciclo          = 1;
            $limite         = $NuevoCompra->Cantidad;


            ## EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL PRODUCTO
            $OrdenDto     =   new ordenCompraDto(
                Fecha_compra                :   $NuevoCompra->Fecha_compra,
                Descripcion                 :   $NuevoCompra->Descripcion,
                marca                       :   $NuevoCompra->marca,
                modelo                      :   $NuevoCompra->modelo,
                Orden_compra                :   $NuevoCompra->Orden_compra,
                Factura_compra              :   $NuevoCompra->Factura_compra,
                Precio_U                    :   $NuevoCompra->Precio_U,
                Cantidad                    :   $NuevoCompra->Cantidad,
                Precio_total                :   $NuevoCompra->Precio_total,
                tipo                        :   $NuevoCompra->tipo,
                idProveedor                 :   $NuevoCompra->Proveedor_idProveedor,
                idEstado_oc                 :   $NuevoCompra->id_estadoOC,
                idEstado_FC                 :   $NuevoCompra->id_estadoFC,
                IdEmpresa                   :   $NuevoCompra->id_empresa
            );

            ## PROCEDEMOS A CREAR EL DTO PARA INSERTAR AL STOCK ACTUAL
            $StockDto     =   new stockDto(
                Fecha                       :   $NuevoCompra->Fecha_compra,
                Descripcion                 :   $NuevoCompra->Descripcion,
                Usuario_asignado            :   $user,
                Cantidad                    :   $ciclo,
                Precio_Unitario             :   $NuevoCompra->Precio_U,
                Precio_total                :   $NuevoCompra->Precio_total,
                IdEmpresa                   :   $NuevoCompra->id_empresa,
                idMarca                     :   $NuevoCompra->marca,
                tipo                        :   $NuevoCompra->tipo,
                estado_stock                :   $estado_stock
            );

            
            ## GUARDAMOS EL NUEVO ITEM EN LA BBDD
            $OrdenDto                   =   $ordenSvc->Insert($OrdenDto);

            ## CICLO PARA INSERTAR EN EL STOCK ACTUAL
            while($ciclo <= $limite){
                $StockDto                   =   $StockSvc->Insert($StockDto);
                $ciclo  = $ciclo+1;
            };


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
        $CompraSvc      = new ordenCompraSvc(ConnectionEnum::TI);

        ## BUSCAMOS EL DTO DEL USUARIO
        $CompraDto         =   $CompraSvc->FindByForeign('idO_C',$DatosCompra->idO_C);

        ## VALIDAMOS SI EL USUARIO EXISTEE DENTRO DE LA BBDD
        if ($CompraDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El producto no se encuentra disponble');
        }

        ## PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{

            $FechaData      =   $DatosCompra->Fecha;
            $FechaData      =   date("Y-m-d", strtotime($FechaData));
            ## 
            $CompraDto->Fecha                            =   $FechaData;
            $CompraDto->Descripcion                      =   $DatosCompra->Descripcion;
            $CompraDto->Cantidad                         =   $DatosCompra->Cantidad;
            $CompraDto->Precio_Unitario                  =   $DatosCompra->Precio_Unitario;
            $CompraDto->Precio_total                     =   $DatosCompra->Precio_total;
            $CompraDto->idMarca                          =   $DatosCompra->idMarca;
            $CompraDto->IdEmpresa                        =   $DatosCompra->IdEmpresa;
            $CompraDto->tipo                             =   $DatosCompra->tipo;
            $CompraDto->estado_stock                     =   $DatosCompra->estado_stock;
            
            ## ACTUALIZAMOS LOS VALORES EN LA BBDD
            $CompraSvc->Update($CompraDto);

            return true;


        } catch (\Exception $ex) {

            return false;

        }

    }


}