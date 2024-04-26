<?php

namespace Application\BLL\BusinessObjects\Core;

use Application\BLL\Services\Core\marcaSvc;
use Application\BLL\Services\Core\modeloSvc;
use Application\BLL\Services\Core\ordencompraSvc;
use Application\BLL\Services\Core\tipoproductoSvc;
use Application\Configuration\ConnectionEnum;
use Application\BLL\DataTransferObjects\Core\ordencompraDto;
use Intouch\Framework\Exceptions\BusinessException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Application\BLL\Services\Core\stockSvc;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Services\Core\VWActivosExcelSvc;
use Application\BLL\Services\Core\VWCompraExcelSvc;
use Application\BLL\Services\Core\VWGastosExcelSvc;
use Intouch\Framework\BLL\Service\GenericSvc;
use Application\Dao\Entities\Core\VWCompraExcel;

class CompraBO 
{

    //FUNCION QUE OBTIENE LOS DATOS A MOSTRAR EN LA TABLA DE COMPRAS
    public function CargarTablaCompra() 
    {
        // EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        // PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        // al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new VWCompraExcelSvc(ConnectionEnum::TI);

        // en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        $DatosCompra  =   $CompraSvc->GetAll();

                    
        // por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    //FUNCION QUE OBTIENE LOS DATOS A MOSTRAR EN LA TABLA DE COMPRAS DE ACTIVOS TI (GENERALES)
    public function CargarTablaGeneralCompras() 
    {
        // EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        // PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        // al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new VWActivosExcelSvc(ConnectionEnum::TI);

        // en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        // para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->GetAll();

                    
        // por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }

    //FUNCION QUE OBTIENE LOS DATOS A MOSTRAR EN LA TABLA DE GASTOS
    public function CargarTablaGastos() 
    {
        // EL PRIMER PASO, ES OBTENER LA LISTA DE DATOS QUE SE ENCUENTRAN GENERADOS EN LA TABLA DE LA BBDD
        // PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
        // al generar el service debemos especificar la conexión a usar (solo nombre)

        $CompraSvc      = new VWGastosExcelSvc(ConnectionEnum::TI);

        // en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        // para esto usamos la propiedad GetAll del ORM interno del framework
        $DatosCompra  =   $CompraSvc->GetAll();

                    
        // por ultimo, retornamos la lista de todos los datos de la tabla
        return $DatosCompra; 
    }
    public function GetTipo($identificador){ 
 

        // INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $tipoService     =   new tipoproductoSvc(ConnectionEnum::TI);

        try{
            if($identificador == 'Agregar Activo'){
    
                $datos          =   $tipoService->BuscarTipoGenerales();
    
            }else{
    
                $datos          =   $tipoService->BuscarTipoGastos();
    
            }       

        } catch (\Exception $ex) {

            // GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    public function GetCompra(int $IdO_C){ 
 

        // INSTANCIAMOS EL SERVICE A UTILIZAR Y LO CONECTAMOS A LA BBDD CORE
        $CompraService     =   new ordencompraSvc(ConnectionEnum::TI);

        try{
            // BUSCAMOS LA INFORMACIÓN DEL USUARIO
            $datos          =   $CompraService->FindByForeign('IdO_C',$IdO_C);

        } catch (\Exception $ex) {

            // GENERAMOS UNA VARIABLE VACIA POR EL ERROR
            $datos = null;
        }

        return $datos;

    }

    //FUNCION LA CUAL INYECTA LOS DATOS A GUARDAR DE UN NUEVO PRODUCTO EN LA BBDD
    public function GuardarProducto($NuevoCompra) : ordencompraDto|null  {


        // VALIDAMOS LOS PARAMETROS DE LA FECHA COMPRA (PARA EVITAR INYECCIONES SQL SE VALIDA ANTES DE GUARDAR)
        if (strlen($NuevoCompra->Fecha_compra) < 10){
            throw new BusinessException(code: ExceptionCodesEnum::ERR_INVALID_PARAMETER, message:'Ha ocurrido un error inesperado');
        }

        //INSTANCIAMOS EL SERVICIO DE ORDERCOMPRA
        $ordenSvc     =   new ordencompraSvc(ConnectionEnum::TI);

        //INSTANCIAMOS EL SERVICIO DE STOCK
        $StockSvc     =   new stockSvc(ConnectionEnum::TI);

        //VALIDAMOS SI ES UNA COMPRA DE TONNER O DE GASTO/ACTIVO
        //SOLO LOS TONNERS TIENE MARCA
        if(!empty($NuevoCompra->idMarca)){

            //OBTENEMOS LA MARCA DEL PRODUCTO EN CUESTION
            $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$NuevoCompra->idMarca);


            //OBTENEMOS EL MODELO DEL PRODUCTO EN CUESTION
            $Modelo         =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$NuevoCompra->idModelo);
    
            GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);
    
            //GENERAMOS UN TRY/CATCH PARA INSERTAR LOS DATOS EN LA BBDD DE ORDEN DE COMPRA
            try{

                //ASIGNAMOS EL VALOR GENERICO DEL PARA INSERTAR EN ALGUNOS CAMPOS DE STOCK
                $user           = "Sin Asignar";

                //ASIGNAMOS EL VALOR GENERICO QUE TIENE UNA COMPRA NUEVA AL INSERTAR EL STOCK
                $estado_stock   = "En Stock";

                //ASIGNAMOS EL VALOR INICIAL DEL CICLO WHILE PARA INSERTAR DATOS
                $ciclo          = 1;

                //ASIGNAMOS EL NUMERO DE VECES QUE SE INSERTARA LOS DATOS AL STOCK 
                $limite         = $NuevoCompra->Cantidad;


                $tipoproducto   = 1;
    
                //ASIGNAMOS EL TIPO DEL STOCK SI ESTE ES ORIGINAL O ALTERNATIVO
                if ($NuevoCompra->tipo == 0){
                    $Tipo = "Original";
                }else{
                    $Tipo = "Alternativo";
                };
    
    
                // EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL PRODUCTO
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
    
                
                // GUARDAMOS EL NUEVO ITEM EN LA BBDD
                $OrdenDto                   =   $ordenSvc->Insert($OrdenDto);
    
                // IF PARA VALIDAR SI SOLO SE INSERTARA UNO O MAS DATOS A LA BBDD DE STOCK
                if ($limite == 1){

                    // PROCEDEMOS A CREAR EL DTO PARA INSERTAR AL STOCK ACTUAL
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
                        estado_stock                :   $estado_stock,
                        idModelo                    :   $NuevoCompra->idModelo
                    );
    
                    //INSERTAMOS LOS DATOS EN LA TABLA DE STOCK
                    $StockDto                   =   $StockSvc->Insert($StockDto);
                }else{
    
                    // PROCEDEMOS A CREAR EL DTO PARA INSERTAR AL STOCK ACTUAL
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
                        estado_stock                :   $estado_stock,
                        idModelo                    :   $NuevoCompra->idModelo
                    );
    
                    // GENERAMOS EL CICLO WHILE PARA INSERTAR LOS DATOS MAS DE UNA VEZ A LA BBDD
                    // ESTO SE DEBE YA QUE SE TIENEN QUE DIVIDIR LOS PRODUCTO SIEMPRE EN 1 AL INSERTAR AL STOCK
                    while($ciclo <= $limite){

                        // INSERTAMOS LA INFORMACION A LA BBDD DE STOCK
                        $StockDto                   =   $StockSvc->Insert($StockDto);

                        // SUMAMOS 1 A LAS VECES QUE TIENE QUE ITERAR EL CICLO PARA PODER SALIR DE ESTE
                        $ciclo  = $ciclo+1;
                    };
                }
    
    
                // EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
                GenericSvc::SaveMultipleOperations();
                return $OrdenDto;
    
            }catch (\Exception $ex) {

                //EN CASO QUE TUVIERAMOS ALGUN PROBLEMA, PROCEDEMOS A REVERTIR LOS CAMBIOS
                GenericSvc::UndoMultipleOperations();
                return null;
    
            }
        }else{
            GenericSvc::BeginMultipleOperations(ConnectionEnum::TI);
    
            //GENERAMOS UN TRY/CATCH PARA INSERTAR LOS DATOS EN LA BBDD DE ORDEN DE COMPRA
            try{

                //ASIGNAMOS EL VALOR GENERICO DEL PARA INSERTAR EN ALGUNOS CAMPOS A ORDEN DE COMPRA
                $Marca = "Sin Marca";
                $Modelo = "Sin Modelo";
                $Tipo = "No Aplica";
                $PrecioU = 0;

                //SI EL VALOR CANTIDAD LLEGA NULL LE OTORGAMOS UN VALOR
                if(empty($NuevoCompra->Cantidad)){
                    $NuevoCompra->Cantidad  =   0;
                }

                if(!empty($NuevoCompra->Proveedor)){

                    // EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL PRODUCTO
                    $OrdenDto     =   new ordencompraDto(
                        Fecha_compra                :   $NuevoCompra->Fecha_compra,
                        Descripcion                 :   $NuevoCompra->Descripcion,
                        marca                       :   $Marca,
                        modelo                      :   $Modelo,
                        Orden_compra                :   $NuevoCompra->Orden_compra,
                        Factura_compra              :   $NuevoCompra->Factura_compra,
                        Precio_U                    :   $PrecioU,
                        Cantidad                    :   $NuevoCompra->Cantidad,
                        Precio_total                :   $NuevoCompra->Precio_total,
                        tipo                        :   $Tipo,
                        idProveedor                 :   $NuevoCompra->idProveedor,
                        idEstado_oc                 :   $NuevoCompra->idEstado_oc,
                        idEstado_FC                 :   $NuevoCompra->idEstado_FC,
                        IdEmpresa                   :   $NuevoCompra->IdEmpresa,
                        idTipoProducto              :   $NuevoCompra->tipo,
                        IdEstadoActivo              :   $NuevoCompra->Estado_Activo
                    );

                    // GUARDAMOS EL NUEVO ITEM EN LA BBDD
                    $OrdenDto                   =   $ordenSvc->Insert($OrdenDto);
    
                }else{
                    // EN PRIMER LUGAR PROCEDEMOS A CREAR EL DTO DEL PRODUCTO
                    $OrdenDto     =   new ordencompraDto(
                        Fecha_compra                :   $NuevoCompra->Fecha_compra,
                        Descripcion                 :   $NuevoCompra->Descripcion,
                        marca                       :   $Marca,
                        modelo                      :   $Modelo,
                        Orden_compra                :   $NuevoCompra->Orden_compra,
                        Factura_compra              :   $NuevoCompra->Factura_compra,
                        Precio_U                    :   $PrecioU,
                        Cantidad                    :   $NuevoCompra->Cantidad,
                        Precio_total                :   $NuevoCompra->Precio_total,
                        tipo                        :   $Tipo,
                        idProveedor                 :   $NuevoCompra->idProveedor,
                        idEstado_oc                 :   $NuevoCompra->idEstado_oc,
                        idEstado_FC                 :   $NuevoCompra->idEstado_FC,
                        IdEmpresa                   :   $NuevoCompra->IdEmpresa,
                        idTipoProducto              :   $NuevoCompra->tipo
                    );

                    // GUARDAMOS EL NUEVO ITEM EN LA BBDD
                    $OrdenDto                   =   $ordenSvc->Insert($OrdenDto);
                }
        
    
    
                // EN CASO DE QUE ESTE TODO CORRECTO, PROCEDEMOS A GUARDAR LOS CAMBIOS
                GenericSvc::SaveMultipleOperations();
                return $OrdenDto;
    
            }catch (\Exception $ex) {

                //EN CASO QUE TUVIERAMOS ALGUN PROBLEMA, PROCEDEMOS A REVERTIR LOS CAMBIOS
                GenericSvc::UndoMultipleOperations();
                return null;
    
            }
        }


    }

    //FUNCION PARA MODIFICAR UNA COMPRA(TONNER, ACTIVO O GASTO)
    public function UpdateCompra($DatosCompra){

        //INSTANCIAMOS EL SERVICIO DE ORDERCOMPRA
        $CompraSvc      = new ordencompraSvc(ConnectionEnum::TI);

        // BUSCAMOS EL DTO DE LA COMPRA CON SU INFORMCAION
        $CompraDto         =   $CompraSvc->FindByForeign('idO_C',$DatosCompra->idO_C);

        // VALIDAMOS SI LA COMPRA EXISTE DENTRO DE LA BBDD
        if ($CompraDto == null){
            throw new BusinessException(code:ExceptionCodesEnum::ERR_INVALID_PARAMETER, message: 'El producto no se encuentra disponble');
        }

        // PROCEDEMOS DENTRO DE UN TRY CATCH A PROCESAR LA SOLICITUD
        try{
            // VALIDAMOS SI MARCA O MODELO VIENE CON DATOS GENERICOS
            if($CompraDto->marca == "Sin Marca" && $CompraDto->modelo == "Sin Modelo"){


                //ASIGNAMOS EL VALOR GENERICO DEL PARA INSERTAR EN ALGUNOS CAMPOS A ORDEN DE COMPRA
                $Marca                  =   "Sin Marca";
                $Modelo                 =   "Sin Modelo";
                $DatosCompra->Precio_U  =   0;
                $Tipo                   =   "No Aplica";

                //ASIGNAMOS UN VALOR A CANTIDAD EN CASO QUE ESTE VENGA COMO NULL
                if(empty($DatosCompra->Cantidad)){
                    $DatosCompra->Cantidad  = (int)0;
                }

            }else{

                //INSTANCIAMOS EL SERVICIO DE MARCA
                $Marca          =   (new marcaSvc(ConnectionEnum::TI))->FindByForeign('idMarca',$DatosCompra->idMarca);

                //GUARDAMOS LA DESCRIPCION PARA MODIFICARLA EN LA BBDD
                $Marca          =   $Marca->Descripcion;

                //INSTANCIAMOS EL SERVICIO DE MODELO
                $Modelo         =   (new modeloSvc(ConnectionEnum::TI))->FindByForeign('idModelo',$DatosCompra->idModelo);

                //GUARDAMOS LA DESCRIPCION PARA MODIFICARLA EN LA BBDD
                $Modelo         =   $Modelo->Descripcion;
            }

            //ESTANDARIZAMOS LA FECHA AL FORMATO DE LA BBDD
            $FechaData      =   $DatosCompra->Fecha_compra;
            $FechaData      =   date("Y-m-d", strtotime($FechaData));

            if($CompraDto->Factura_compra == ""){

                $CompraDto->Factura_compra                  =   0;

            }else{

                $CompraDto->Factura_compra                  =   $DatosCompra->Factura_compra;

            }

            //INYECTAMOS LOS NUEVOS DATOS QUE TENDRA EL PRODUCTO EN EL DTO
            $CompraDto->Fecha_compra                    =   $FechaData;
            $CompraDto->Descripcion                     =   $DatosCompra->Descripcion;
            $CompraDto->Orden_compra                    =   $DatosCompra->Orden_compra;
            $CompraDto->Cantidad                        =   $DatosCompra->Cantidad;
            $CompraDto->Precio_U                        =   $DatosCompra->Precio_U;
            $CompraDto->Precio_total                    =   $DatosCompra->Precio_total;
            $CompraDto->marca                           =   $Marca;
            $CompraDto->modelo                          =   $Modelo;
            $CompraDto->IdEmpresa                       =   $DatosCompra->IdEmpresa;
            $CompraDto->tipo                            =   $Tipo;
            $CompraDto->idProveedor                     =   $DatosCompra->idProveedor;
            $CompraDto->idEstado_oc                     =   $DatosCompra->idEstado_oc;
            $CompraDto->idEstado_FC                     =   $DatosCompra->idEstado_FC;

            // ACTUALIZAMOS LOS VALORES DEL PRODUCTO EN LA BBDD
            $CompraSvc->Update($CompraDto);

            return true;


        } catch (\Exception $ex) {

            return false;

        }

    }


}