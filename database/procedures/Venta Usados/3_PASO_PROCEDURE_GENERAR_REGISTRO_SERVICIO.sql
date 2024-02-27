DELIMITER //

CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_GUARDAR_NEGOCIOS_SERVICIOS_USADOS(
        IN Temporada_Input int,
	    IN Periodo_Input int  
)
BEGIN 


	## ----------------------------------------------------------------------------------------------------------
	## EN PRIMER LUGAR PROCEDEMOS A GENERAR LA TABLA TEMPORAL PARA FACILITAR LOS TIEMPOS DE EJECUCIÓN
	## ----------------------------------------------------------------------------------------------------------
	
	## LIMPIAMOS LA MEMORIA DE LA TABLA TEMPORA
	DROP TABLE IF EXISTS Autogestores_Warehouse.temp_nv_table;

	## GENERAMOS LA TABLA TEMPORAL QUE VA A CONTENER LAS NOTAS DE VENTA A TRABAJAR
	CREATE TEMPORARY TABLE 	IF NOT EXISTS Autogestores_Warehouse.temp_nv_table SELECT * FROM  Stock_en_linea.SIGA_NOTAS_DE_VENTA	 LIMIT 0;

	## GUARDAMOS LOS DATOS QUE VAMOS A UTILIZAR EN LA TABLA TEMPORAL
	INSERT INTO Autogestores_Warehouse.temp_nv_table
	SELECT 
		* 
	FROM Stock_en_linea.SIGA_NOTAS_DE_VENTA		
	WHERE YEAR(Fecha_Factura) = Temporada_Input AND MONTH(Fecha_Factura) = Periodo_Input;

	## ----------------------------------------------------------------------------------------------------------





    ## ------------------------------------------------------------------------------------------------------------------------------------------------------
    ## GUARDAMOS LAS NOTAS DE VENTA COMO SERVICIO Y LO ASOCIAMOS A UN CLIENTE
    ## EN ESPECIFICO
    ## ------------------------------------------------------------------------------------------------------------------------------------------------------
    

## INCORPORAMOS LAS NOTAS DE VENTA FACTURADAS FALTANTES AL MODELO
    INSERT INTO Autogestores_Warehouse.Servicio (IdCliente,IdTipoServicio,IdCentro,FechaServicio ,IdPlataformaOrigen,IdUsuarioPropietario,CodigoServicio,NumeroTelefono,Correo,Nombre)
    SELECT 
		c.IdClientes,
		2 as TipoServicio,
		c2.IdCentro,
		nv.Fecha_Factura ,
		2 as PlataformaOrigen,
		null as UsuarioPropietario,	
		CONCAT( TRIM(nv.N_Venta),TRIM(nv.N_Factura) )  as CodigoServicio,
		nv.Telefono,
		nv.EMAIL,
		nv.Cliente 
	FROM Autogestores_Warehouse.temp_nv_table nv
		JOIN Autogestores_Warehouse.Clientes c ON c.Rut  = TRIM( REPLACE(nv.RUT,".","") )
		JOIN Autogestores_Warehouse.Centro c2 ON c2.Descripcion  = trim(nv.`Local`) AND c2.IdTipoCentro = 3
	AND NOT EXISTS ( SELECT * FROM Autogestores_Warehouse.Servicio s WHERE s.CodigoServicio = CONCAT( TRIM(nv.N_Venta),TRIM(nv.N_Factura) ) AND s.IdTipoServicio = 2 ) AND nv.N_Factura IS NOT NULL ;




    ## UNA VEZ CREADOS PROCEDEMOS A GUARDAR EL DETALLE DE LAS NOTAS DE VENTA
    INSERT INTO Autogestores_Warehouse.ServicioVenta (IdServicio,ClasePedido,Marca,Modelo,Patente,Chasis,Color,NumeroPedido,FechaPedido,NumeroFactura,FechaFactura,TipoPedido,AñoModelo,IndCompraPara,DPS,DPE,PrecioLista,PrecioCompra,Descuentos,Accesorios,PrecioVenta,CargosVehiculo,GastosProvicionados,Margen,NumeroStock,CodigoVehiculo,FechaRecepcion,AutorizadoPor,InstitucionCredito,ValorCredito,MarcaRetoma,ModeloRetoma,AñoRetoma,ValorRetoma,CodigoRetoma,PatenteRetoma,IdVehiculo,MundoVenta)
    SELECT 
        s.IdServicio,
        SUBSTRING(nv.N_Factura,1,3) as ClasePedido,
        nv.Marca,
        nv.Modelo,
        nv.Patente,
        nv.Chassis,
        nv.Color,
        nv.N_Venta as NumeroPedido,
        nv.Fecha,
        nv.N_Factura,
        nv.Fecha_Factura,
        SUBSTRING(nv.N_Factura,1,3) as ClasePedido,
        nv.Año as AnioModelo,
        CASE WHEN nv.RUT_compra_para IS NULL THEN FALSE ELSE TRUE END as IndCompraPara,
        nv.DPS,
        nv.DPE,
        nv.Precio_de_Lista,
        nv.Precio_de_Compra,
        nv.Descuentos,
        nv.Accesorios * 1 as Accesorios,
        nv.Precio_de_Venta ,
        nv.Cargos_a_Vehiculos,
        nv.Gastos_Provisionados,
        null,
        nv.N_Stock,
        nv.Codigo_Vehiculo,
        nv.Fecha_Recepcion,
        nv.Autorizada_Por,
        nv.Institucion_Credito ,
        nv.Valor_Credito * 1 as ValorCredito,
        nv.Marca_Retoma,
        nv.Modelo_Retoma,
        nv.Año_Retoma,
        nv.Valor_Retoma * 1,
        nv.VU_Retoma,
        nv.Patente_Retoma,
        null,
        "USADOS"
    FROM Autogestores_Warehouse.temp_nv_table nv
        JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = CONCAT( TRIM(nv.N_Venta),TRIM(nv.N_Factura) )
    WHERE nv.Fecha_Factura IS NOT NULL
        AND NOT EXISTS ( SELECT * FROM Autogestores_Warehouse.ServicioVenta s2 WHERE s2.IdServicio = s.IdServicio  )
    GROUP BY s.IdServicio
    ORDER BY s.IdServicio  ASC; 
        

END
//
 