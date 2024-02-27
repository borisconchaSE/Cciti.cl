
DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_CARGAR_CLIENTES_NUEVOS(
    IN Temporada_Input int,
    IN Periodo_Input int
)
BEGIN 

	## ----------------------------------------------------------------------------------------------------------
	## EN PRIMER LUGAR PROCEDEMOS A GENERAR LA TABLA TEMPORAL PARA FACILITAR LOS TIEMPOS DE EJECUCIÓN
	## ----------------------------------------------------------------------------------------------------------
	
	## LIMPIAMOS LA MEMORIA DE LA TABLA TEMPORA
	DROP TABLE IF EXISTS Autogestores_Warehouse.temp_nv_table_BEXUNO;
	DROP TABLE IF EXISTS Autogestores_Warehouse.temp_nv_table_BEXDOS;

	## GENERAMOS LA TABLA TEMPORAL QUE VA A CONTENER LAS NOTAS DE VENTA A TRABAJAR
	CREATE TEMPORARY TABLE 	IF NOT EXISTS Autogestores_Warehouse.temp_nv_table_BEXUNO SELECT * FROM  prodDatafile_dev.SAP_BEX1 				LIMIT 0;
	CREATE TEMPORARY TABLE 	IF NOT EXISTS Autogestores_Warehouse.temp_nv_table_BEXDOS SELECT * FROM  prodDatafile_dev.SAP_BEX4_VENTA_OSORNO LIMIT 0;


	## GUARDAMOS LOS DATOS QUE VAMOS A UTILIZAR EN LA TABLA TEMPORAL
	INSERT INTO Autogestores_Warehouse.temp_nv_table_BEXUNO
	SELECT * FROM prodDatafile_dev.SAP_BEX1 s WHERE YEAR(s.dia_natural) = Temporada_Input AND MONTH(s.dia_natural) = Periodo_Input;

	INSERT INTO Autogestores_Warehouse.temp_nv_table_BEXDOS
	SELECT * FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO s WHERE YEAR(s.dia_natural) = Temporada_Input AND MONTH(s.dia_natural) = Periodo_Input;

	
    ## -------------------------------------------------------------------------------------------------------------------------------------
    ## EN CASO DE SER NECESARIO, BORRAMOS LA BBDD TEMPORALES PARA AGREGAR LOS NUEVOS CAMPOS
    ## -------------------------------------------------------------------------------------------------------------------------------------
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_nuevos;
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_nuevos_osorno;



    ## ---------------------------------------------------------------------------------------------------
    ## GENERAMOS EL FLUJO DE CLIENTES DE VENTA NUEVOS
    ## ---------------------------------------------------------------------------------------------------

    ## GENERAMOS UNA TABLA TEMPORAL PARA ALMACENAR LOS RUTS DE LOS CLIENTES
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_clientes_nuevos(
        nombre varchar(255) not null,
        rut varchar(255) 	not null,
        telefono_primario 	VARCHAR(125) null,
        telefono_movil 		VARCHAR(125) null,
        telefono_secundario VARCHAR(125) null,
        email 				VARCHAR(125) null
    );

    ## GENERAMOS LA TABLA TEMPORAL QUE ALMACENARÁ LOS RUTS DE LOS CLIENTES DE OSORNO
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_clientes_nuevos_osorno(
        nombre varchar(255) not null,
        rut varchar(255) 	not null,
        telefono_primario 	VARCHAR(125) null,
        telefono_movil 		VARCHAR(125) null,
        telefono_secundario VARCHAR(125) null,
        email 				VARCHAR(125) null
    );



    ## INSERTAMOS LA LISTA DE LOS CLIENTES PRESENTES EN EL AÑOS SELECCIONADO EN LA FACTURACIÓN
    INSERT INTO Autogestores_Warehouse.temp_lista_clientes_nuevos (	nombre,	rut,	telefono_primario,	telefono_movil,	telefono_secundario,	email) 
    SELECT
    sb.cliente,
    sb.numero_id_fiscal as rut,
    sb.telefono_primario,
    sb.telefono_movil,
    sb.telefono_secundario,
    sb.email
    FROM Autogestores_Warehouse.temp_nv_table_BEXUNO sb
    WHERE sb.id_centro IN (
        "C007",
        "C009",
        "C010",
        "C012",
        "C108",
        "C155",
        "C011"
    )
    GROUP BY sb.numero_id_fiscal 
    ORDER BY sb.dia_natural DESC;




    ## INSERTAMOS LA LISTA DE LOS CLIENTES PRESENTES EN EL AÑOS SELECCIONADO EN LA FACTURACIÓN DE OSORNO
    INSERT INTO Autogestores_Warehouse.temp_lista_clientes_nuevos_osorno (	nombre,	rut,	telefono_primario,	telefono_movil,	telefono_secundario,	email) 
    SELECT
    sb.nombre_cliente ,
    sb.numero_id_fiscal as rut,
    sb.telefono_1 ,
    sb.telefono_movil,
    sb.telefono_secundario,
    sb.correo 
    FROM Autogestores_Warehouse.temp_nv_table_BEXDOS sb
    WHERE sb.id_centro IN (
        "C170"
    )  AND NOT EXISTS ( SELECT * FROM Autogestores_Warehouse.temp_lista_clientes_nuevos s2 WHERE s2.rut = TRIM( REPLACE(sb.numero_id_fiscal ,".","") ) )
    AND YEAR(sb.dia_natural) = Temporada_Input
    GROUP BY sb.numero_id_fiscal 
    ORDER BY sb.dia_natural DESC;








    ## INGRESAMOS TODOS LOS CLIENTES DE SEM QUE NO ESTEN PRESENTES EN LA BBDD DE AUTOGESTORES
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad, Rut, Activo)
    SELECT
    sb.nombre,
    sb.email,
    sb.telefono_primario,
    sb.telefono_movil,
    sb.telefono_secundario,
    1,
    sb.rut,
    1
    FROM Autogestores_Warehouse.temp_lista_clientes_nuevos sb
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Clientes c WHERE c.Rut = TRIM( REPLACE(sb.rut,".","") )  );


    ## INGRESAMOS TODOS LOS CLIENTES DE SPA QUE NO ESTEN PRESENTES EN LA BBDD DE AUTOGESTORES
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad, Rut, Activo)
    SELECT
    sb.nombre,
    sb.email,
    sb.telefono_primario,
    sb.telefono_movil,
    sb.telefono_secundario,
    1,
    sb.rut,
    1
    FROM Autogestores_Warehouse.temp_lista_clientes_nuevos_osorno sb
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Clientes c WHERE c.Rut = TRIM( REPLACE(sb.rut,".","") )  );

END
//


