DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_USADOS_CARGA_CLIENTES(
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
	FROM Stock_en_linea.SIGA_NOTAS_DE_VENTA s		
	WHERE YEAR(s.Fecha_Factura) = Temporada_Input AND MONTH(s.Fecha_Factura) = Periodo_Input;

	
	
	
	
	
	

    ## ----------------------------------------------------------------------------------------------------------
    ## INGRESA LAS COMUNAS DISPONIBLES EN LA BBDD
    ## ----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Comuna (IdRegion,Descripcion)
    SELECT 1, trim(nv.Comuna)  FROM Autogestores_Warehouse.temp_nv_table nv
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Comuna c WHERE c.Descripcion = trim(nv.Comuna)) AND LENGTH( trim(nv.Comuna) ) > 4
    GROUP BY nv.Comuna;
    ## ----------------------------------------------------------------------------------------------------------



    ## ----------------------------------------------------------------------------------------------------------
    ## INGRESA LAS CIUDADES DISPONIBLES EN LA BBDD
    ## ----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Ciudad (IdComuna,Descripcion)
    SELECT 
    c.IdComuna ,
    trim(nv.Ciudad) 
    FROM Autogestores_Warehouse.temp_nv_table nv
    JOIN Autogestores_Warehouse.Comuna c ON c.Descripcion  = trim(nv.Comuna)
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Ciudad c2 WHERE c2.Descripcion = trim(nv.Ciudad))
    AND LENGTH( TRIM( nv.Ciudad  ) ) > 4
    GROUP BY nv.Ciudad;
    ## ----------------------------------------------------------------------------------------------------------


    ## ----------------------------------------------------------------------------------------------------------
    ## INGRESAMOS LOS CLIENTES NO EXISTENTES EN LA BBDD
    ## ----------------------------------------------------------------------------------------------------------
	INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo)
	SELECT
    nv.Cliente,
    nv.EMAIL,
    IF( LENGTH(trim(REGEXP_REPLACE(nv.Telefono         , '[^0-9]+', ''  )))     < 8, null , TRIM(  REGEXP_REPLACE(nv.Telefono         , '[^0-9]+', ''  ) ) * 1 ),
    IF( LENGTH(trim(REGEXP_REPLACE(nv.Telefono_Celular , '[^0-9]+', ''  )))     < 8, null , TRIM(  REGEXP_REPLACE(nv.Telefono_Celular , '[^0-9]+', ''  ) ) * 1 ),
    IF( LENGTH(trim(REGEXP_REPLACE(nv.Telefono_Oficina , '[^0-9]+', ''  )))     < 8, null , TRIM(  REGEXP_REPLACE(nv.Telefono_Oficina , '[^0-9]+', ''  ) ) * 1 ),
    IFNULL(c2.IdCiudad,1) ,
    TRIM(REPLACE(nv.RUT, ".","")),
    1
    FROM Autogestores_Warehouse.temp_nv_table nv
    LEFT JOIN Autogestores_Warehouse.Ciudad c2  ON trim(nv.Ciudad) = c2.Descripcion 
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Clientes c WHERE c.Rut = TRIM(REPLACE(nv.RUT, ".","")) )
    GROUP BY nv.RUT
   	ORDER BY nv.Fecha_Factura DESC;
    ## ------------------------------------------------------------------------------------------- 

END//