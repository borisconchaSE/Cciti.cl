DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_VINCULAR_VEHICULOS_USADOS(
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
	CREATE TEMPORARY TABLE 	IF NOT EXISTS Autogestores_Warehouse.temp_nv_table SELECT * FROM  Stock_en_linea.SIGA_NOTAS_DE_VENTA LIMIT 0;

	## GUARDAMOS LOS DATOS QUE VAMOS A UTILIZAR EN LA TABLA TEMPORAL
	INSERT INTO Autogestores_Warehouse.temp_nv_table
	SELECT 
		* 
	FROM Stock_en_linea.SIGA_NOTAS_DE_VENTA		
	WHERE YEAR(Fecha_Factura) = Temporada_Input AND MONTH(Fecha_Factura) = Periodo_Input;

	## ----------------------------------------------------------------------------------------------------------


	## -----------------------------------------------------------------------------------------
	## CREAMOS TODOS LOS CENTROS DISPONIBLES EN LAS NOTAS DE VENTA A GUARDAR EN LA BBDD
	## -----------------------------------------------------------------------------------------
	INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal)
	SELECT 
	DISTINCT REPLACE(TRIM(s.`Local`), " ", "_"),
	s.`Local`,
	1
	FROM Autogestores_Warehouse.temp_nv_table s
	WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Centro c WHERE c.Codigo = REPLACE(TRIM(s.`Local`), " ", "_"));



	#DELETE FROM Autogestores_Warehouse.VehiculoCliente ;
	#ALTER TABLE Autogestores_Warehouse.VehiculoCliente AUTO_INCREMENT = 1;
	
	#DELETE FROM Autogestores_Warehouse.Vehiculos  ;
	#ALTER TABLE Autogestores_Warehouse.Vehiculos AUTO_INCREMENT = 1;


	## -----------------------------------------------------------------------------------------
	## GENERAMOS TODOS LOS VEHICULOS DISPONIBLES EN LA BBDD DE VENTAS DE USADOS CON PATENTE
	## -----------------------------------------------------------------------------------------
	INSERT INTO Autogestores_Warehouse.Vehiculos (Marca,Modelo,Patente,NumeroChasis,FechaIngreso,Kilometraje,AñoModelo)
		SELECT 
		s.Marca,
		s.Modelo,
		TRIM(s.Patente),
		s.Chassis,
		s.Fecha_Factura,
		s.Kilometraje,
		s.Año 
		FROM Autogestores_Warehouse.temp_nv_table s
		LEFT JOIN Autogestores_Warehouse.Clientes c ON c.Rut = REPLACE(s.RUT, ".","")
		WHERE s.Patente IS NOT NULL AND s.Fecha_Factura IS NOT NULL
		AND NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Vehiculos v WHERE TRIM(v.Patente)  =  TRIM(s.Patente) )
		GROUP BY s.Patente 
		ORDER BY s.Fecha DESC;


	## -----------------------------------------------------------------------------------------
	## TRATAMOS DE VINCULAR LOS VEHICULOS SIN PATENTE POR NUMERO DE CHASIS
	## -----------------------------------------------------------------------------------------
	INSERT INTO Autogestores_Warehouse.Vehiculos (Marca,Modelo,Patente,NumeroChasis,FechaIngreso,Kilometraje,AñoModelo)
		SELECT  
		s.Marca,
		s.Modelo,
		TRIM(s.Patente),
		s.Chassis,
		s.Fecha_Factura,
		s.Kilometraje,
		s.Año
		FROM Autogestores_Warehouse.temp_nv_table s
		LEFT JOIN Autogestores_Warehouse.Clientes c ON c.Rut = REPLACE(s.RUT, ".","")
		WHERE s.Patente IS NULL AND s.Fecha_Factura IS NOT NULL and s.Chassis is not null 
		AND NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Vehiculos v WHERE TRIM(v.NumeroChasis)  =  TRIM(s.Chassis) )
		ORDER BY s.Fecha_Factura  DESC;
		
		


	## -----------------------------------------------------------------------------------------
	## VINCULAMOS LOS VEHICULOS A LOS CLIENTES POR CHASIS
	## -----------------------------------------------------------------------------------------
	## GENERAMOS LA TABLA TEMPORAL QUE NOS PERMITIRÁ VINCULAR RAPIDAMENTE LAS UNIDADES A LOS CLIENTES
	CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos(
	Chassis varchar(255) not null,
	RUT varchar(255) not null,
	Fecha DATETIME not null
	);

	## LIMPIAMOS LA BASE DE DATOS TEMPORAL
	TRUNCATE  Autogestores_Warehouse.temp_lista_clientes_vehiculos;

	## INCORPORAMOS LOS DATOS DE LOS VEHICULOS A LA LISTA TEMPORAL DE CLIENTES Y VEHICULOS
	INSERT INTO Autogestores_Warehouse.temp_lista_clientes_vehiculos(Chassis, RUT,Fecha)
		SELECT 
		nv.Chassis,
		TRIM(REPLACE(nv.RUT, ".","")),
		nv.Fecha_Factura
		FROM Autogestores_Warehouse.temp_nv_table nv
		WHERE nv.N_Factura IS NOT NULL AND nv.Chassis IS NOT NULL
		GROUP BY nv.Patente;

	## VINCULAMOS LOS VEHICULOS CON LOS CLIENTES
	INSERT INTO Autogestores_Warehouse.VehiculoCliente (IdVehiculo,IdCliente,Fecha,IndActual)
		SELECT 
		v.IdVehiculo,
		c.IdClientes,
		tp.Fecha,
		0
		FROM Autogestores_Warehouse.temp_lista_clientes_vehiculos tp
		JOIN Autogestores_Warehouse.Clientes c ON c.Rut = tp.RUT
		JOIN Autogestores_Warehouse.Vehiculos v ON v.NumeroChasis = tp.Chassis
		WHERE NOT EXISTS ( SELECT * FROM Autogestores_Warehouse.VehiculoCliente vc WHERE vc.IdCliente = c.IdClientes  AND vc.IdVehiculo = v.IdVehiculo  );

	## -----------------------------------------------------------------------------------------

END//