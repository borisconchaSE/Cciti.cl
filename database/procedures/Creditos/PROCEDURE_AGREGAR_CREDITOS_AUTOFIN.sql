DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_AGREGAR_CREDITOS_AUTOFIN(
 	IN Temporada_Input int,
 	IN Periodo_Input int
 )
    BEGIN 
    ## -----------------------------------------------------------------------------------------
    ## INICIAMOS EL PROCEDURE QUE NOS PERMITE ALMACENAR LOS CREDITOS DE AUTOFIN EN AUTOGESTORES
    ## -----------------------------------------------------------------------------------------


	## ----------------------------------------------------------------------------------------------------------
	## EN PRIMER LUGAR PROCEDEMOS A GENERAR LA TABLA TEMPORAL PARA FACILITAR LOS TIEMPOS DE EJECUCIÓN
	## ----------------------------------------------------------------------------------------------------------
	
	## LIMPIAMOS LA MEMORIA DE LA TABLA TEMPORA
	DROP TABLE IF EXISTS Autogestores_Warehouse.temp_nv_table;

	## GENERAMOS LA TABLA TEMPORAL QUE VA A CONTENER LAS NOTAS DE VENTA A TRABAJAR
	CREATE TEMPORARY TABLE 	IF NOT EXISTS Autogestores_Warehouse.temp_nv_table SELECT * FROM prodDatafile_dev.AUTOFIN_GESTION_DIARIA LIMIT 0;

	## GUARDAMOS LOS DATOS QUE VAMOS A UTILIZAR EN LA TABLA TEMPORAL
	INSERT INTO Autogestores_Warehouse.temp_nv_table
	SELECT 
		* 
	FROM prodDatafile_dev.AUTOFIN_GESTION_DIARIA s		
	WHERE YEAR(s.`Fecha Curse` ) = Temporada_Input AND MONTH(s.`Fecha Curse` ) = Periodo_Input;

 










    ## ---------------------------------------------------------------------------------------
    ## EN PRIMER LUGAR INGRESAMOS LOS CENTROS A LA BBDD
    ## ---------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Centro (Codigo, Descripcion, IdSucursal, IdTipoCentro)
    SELECT 
    TRIM(a.Sucursal) as Codigo,
    TRIM(a.Sucursal) as Descripcion,
    1 as IdSucursal,
    5 as TipoCentro
    FROM Autogestores_Warehouse.temp_nv_table a
    WHERE NOT EXISTS (
        SELECT
            *
        FROM Autogestores_Warehouse.Centro c WHERE c.Codigo = TRIM(a.Sucursal) 
    )
    GROUP BY a.Sucursal;



    ## ---------------------------------------------------------------------------------------
    ## UNA VEZ INGRESADO LOS CENTRO CORRESPONDIENTES, PROCEDEMOS A INGRESAR LOS 
    ## LOS CLIENTES NO EXISTENTES EN LA BBDD
    ## ---------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo,FechaCreacion)
    SELECT 
    s.`Nombre Cliente Empresa`,
    s.Email,
    s.Telefono,
    null as TelefonoSecundario,
    null as TelefonoOpcional,
    1 as Ciudad,
    TRIM(REPLACE(s.`Rut cliente`, ".","") ) as Rut,
    CASE WHEN LENGTH(TRIM(REPLACE(s.`Rut cliente`, ".","") )) > 6 THEN 1 ELSE 0 END as Activo,
    s.`Fecha Curse` as Fecha
    FROM Autogestores_Warehouse.temp_nv_table s
    WHERE NOT EXISTS (
        SELECT 
            *	
        FROM Autogestores_Warehouse.Clientes c 
        WHERE c.Rut  = TRIM(REPLACE(s.`Rut cliente`, ".","") )
    )
    GROUP BY s.`Rut cliente`;


    ## --------------------------------------------------------------------
    ## UNA VEZ INGRESADOS LOS CLIENTES, PROCEDEMOS A INGRESAR LOS SERVICIOS
    ## --------------------------------------------------------------------

    ## LIMPIAMOS LA TABLA TEMPORAL EN CASO DE QUE EXISTA
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_Lista_Creditos_Autofin;

    ## GENERAMOS LA NUEVA TABLA TEMPORAL
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_Lista_Creditos_Autofin(
    IdCliente	int NULL,
    IdTipoServicio	int NULL,
    IdCentro	int NULL,
    FechaServicio	datetime NULL,
    IdPlataformaOrigen	int NULL,
    IdUsuarioPropietario	int NULL,
    CodigoServicio	varchar(100) NULL,
    NumeroTelefono	varchar(100) NULL,
    Correo	varchar(100) NULL,
    Nombre	varchar(100) NULL,
    Ciudad	varchar(255) NULL,
    Comuna	varchar(255) NULL
    );

    ## ALMACENAMOS EN LA TABLA TEMPORAL LOS DATOS A TRABAJAR
    INSERT INTO Autogestores_Warehouse.temp_Lista_Creditos_Autofin (IdCliente ,IdTipoServicio ,IdCentro ,FechaServicio ,IdPlataformaOrigen ,IdUsuarioPropietario ,CodigoServicio ,NumeroTelefono ,Correo ,Nombre ,Ciudad ,Comuna)
    SELECT 
    c2.IdClientes ,
    7 as IdTipoServicio,
    c.IdCentro,
    a.`Fecha Curse` as FechaServicio,
    4 as IdPlataforma,
    null as IdPropietario,
    a.ID as  CodigoServicio ,
    a.Telefono,
    a.Email,
    a.`Nombre Cliente Empresa`,
    null as Ciudad,
    null as Comuna
    FROM Autogestores_Warehouse.temp_nv_table a
    JOIN Autogestores_Warehouse.Centro c 	ON c.Codigo = TRIM(a.Sucursal)
    JOIN Autogestores_Warehouse.Clientes c2 ON TRIM(REPLACE(a.`Rut cliente`, ".","") ) = c2.Rut 
    GROUP BY a.ID;

    ## INSERTAMOS EN LOS SERVICIOS LOS DATOS ALMACENADOS EN LA TABLA TEMPORAL
    INSERT INTO Autogestores_Warehouse.Servicio (IdCliente, IdTipoServicio, IdCentro, FechaServicio, IdPlataformaOrigen, IdUsuarioPropietario, CodigoServicio, NumeroTelefono,Correo,Nombre,Ciudad,Comuna)
    SELECT 
    a.IdCliente,
    a.IdTipoServicio,
    a.IdCentro,
    a.FechaServicio,
    a.IdPlataformaOrigen,
    a.IdUsuarioPropietario,
    a.CodigoServicio,
    a.NumeroTelefono,
    a.Correo,
    a.Nombre,
    a.Ciudad,
    a.Comuna
    FROM Autogestores_Warehouse.temp_Lista_Creditos_Autofin a
    WHERE NOT EXISTS (
        SELECT 
        *
        FROM Autogestores_Warehouse.Servicio s 
        WHERE s.CodigoServicio = a.CodigoServicio  AND s.IdTipoServicio = 7
    );

    ## VOLVEMOS A ELIMINAR LA TABLA TEMPORAL PARA LIBERAR ESPACIO EN LA MEMORIA
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_Lista_Creditos_Autofin;



    ## PROCEDEMOS A GUARDAR LOS ESTADOS DEL CREDITO
    INSERT INTO Autogestores_Warehouse.EstadoCredito (Descripcion)
    SELECT 
    a.Estado 
    FROM Autogestores_Warehouse.temp_nv_table a
    WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.EstadoCredito ec WHERE ec.Descripcion = a.Estado)
    GROUP BY a.Estado ;

    







    ## GENERAMOS LA TABLA TEMPORAL QUE VA A ALMACENAR LOS DATOS DE FORMA TEMPORAL
    ## PARA AGILIZAR TODOS LOS CRUCES Y VALIDACIONES
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_Lista_Creditos_Autofin_Detalle;

    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_Lista_Creditos_Autofin_Detalle(
        IdServicio	int NULL ,
        Marca	varchar(45) NULL ,
        Modelo	varchar(255) NULL ,
        AñoModelo	int NULL ,
        NombreProducto	varchar(255) NULL ,
        EstadoAuto	varchar(45) NULL ,
        Precio	int NULL ,
        Pie	int NULL ,
        MontoPagare	int NULL ,
        Plazo	int NULL ,
        SaldoPrecio	int NULL ,
        TasaCurse	int NULL ,
        ValorCuota	int NULL ,
        Sueldo	int NULL ,
        TipoVehiculo	varchar(45) NULL ,
        Canal	varchar(45) NULL ,
        IdEstadoCredito	int NULL ,
        Vendedor	varchar(255) NULL ,
        InstituciónFinanciera	varchar(255) NULL 
    );

    



    ## PROCEDEMOS A REALIZAR EL MISMO EJERCICIO PERO ESTA VEZ CON EL DETALLE DEL SERVICIO
    INSERT INTO  Autogestores_Warehouse.temp_Lista_Creditos_Autofin_Detalle (IdServicio, Marca, Modelo, AñoModelo, NombreProducto, EstadoAuto, Precio, Pie, MontoPagare, Plazo, SaldoPrecio, TasaCurse, ValorCuota, Sueldo, TipoVehiculo, Canal, IdEstadoCredito, Vendedor, InstituciónFinanciera)
    SELECT 
    a.ID  ,
    a.Marca,
    a.Modelo,
    a.Año,
    a.`Nombre de Producto`,
    a.`Estado del auto`,
    a.Precio,
    a.Pie,
    a.`Monto Pagare`,
    a.Plazo,
    a.`Saldo precio`,
    a.`Tasa Curse`,
    a.`Valor cuota`,
    a.Sueldo,
    a.`Tipo vehículo` ,
    a.Canal,
    ec.IdEstadoCredito ,
    Vendedor,
    null as InstitucionCredito
    FROM Autogestores_Warehouse.temp_nv_table a
    JOIN Autogestores_Warehouse.EstadoCredito ec ON ec.Descripcion = a.Estado 
    GROUP BY a.ID;

    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_Lista_CreditosDetalle;
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_Lista_CreditosDetalle 
    SELECT * FROM Autogestores_Warehouse.Servicio s WHERE s.IdTipoServicio = 7 AND YEAR(s.FechaServicio) = Temporada_Input AND MONTH(s.FechaServicio) = Periodo_Input LIMIT 0;
   	
   	INSERT INTO Autogestores_Warehouse.temp_Lista_CreditosDetalle 
   	SELECT * FROM Autogestores_Warehouse.Servicio s WHERE s.IdTipoServicio = 7 AND YEAR(s.FechaServicio) = Temporada_Input AND MONTH(s.FechaServicio) = Periodo_Input;
   
   
    
   
   INSERT INTO Autogestores_Warehouse.ServicioCredito (IdServicio, Marca, Modelo, AñoModelo, NombreProducto, EstadoAuto, Precio, Pie, MontoPagare, Plazo, SaldoPrecio, TasaCurse, ValorCuota, Sueldo, TipoVehiculo, Canal, IdEstadoCredito, Vendedor, InstituciónFinanciera)    
    SELECT 
    s.IdServicio,
    a.Marca,
    a.Modelo,
    a.AñoModelo,
    a.NombreProducto,
    a.EstadoAuto,
    a.Precio,
    a.Pie,
    a.MontoPagare,
    a.Plazo,
    a.SaldoPrecio,
    a.TasaCurse,
    a.ValorCuota,
    a.Sueldo,
    a.TipoVehiculo,
    a.Canal,
    a.IdEstadoCredito,
    a.Vendedor,
    a.InstituciónFinanciera
    FROM Autogestores_Warehouse.temp_Lista_Creditos_Autofin_Detalle a 
    JOIN Autogestores_Warehouse.temp_Lista_CreditosDetalle s ON s.IdTipoServicio = 7 AND s.CodigoServicio =  a.IdServicio
    WHERE NOT EXISTS 
    (
    	SELECT * FROM Autogestores_Warehouse.ServicioCredito sc WHERE sc.IdServicio = s.IdServicio
    )
	GROUP BY a.IdServicio;
 
 
	 
   
   
   
END
//
    