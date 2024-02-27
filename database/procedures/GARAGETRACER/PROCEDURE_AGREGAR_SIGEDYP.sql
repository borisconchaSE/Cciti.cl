
DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_INGRESO_SIGEDYP(
    IN Temporada_Input int 
)
BEGIN 

    ## -----------------------------------------------------------------------------
    ## GENERAMOS EL FLUJO QUE NOS VA A PERMITIR INGRESAR LOS DATOS DE SIGEDYP
    ## -----------------------------------------------------------------------------

    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_table_sigedyp;

    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_table_sigedyp(
        id	bigint null ,
        ot_cotizacion	bigint null ,
        ot_trabajo	text null ,
        en_proceso	tinyint(1) null ,
        created_at	timestamp null ,
        asesor	text null ,
        motivo_pedido	text null ,
        descripcion_motivo_pedido	text null ,
        danio	text null ,
        fecha_ingreso_taller	timestamp null ,
        fecha_retiro_taller	timestamp null ,
        aseguradora	text null ,
        liquidador	text null ,
        siniestro	double null ,
        vehiculo_id	bigint null ,
        vehiculo_patente	text null ,
        vehiculo_modelo	text null ,
        vehiculo_marca	text null ,
        vehiculo_color	text null ,
        vehiculo_anio	text null ,
        vehiculo_motor	text null ,
        vehiculo_chasis	text null ,
        vehiculo_kilometraje	text null ,
        vehiculo_created_at	datetime null ,
        vehiculo_updated_at	datetime null ,
        cliente_id	bigint null ,
        cliente_rut	text null ,
        cliente_nombres	text null ,
        cliente_telefono	text null ,
        cliente_email	text null ,
        cliente_created_at	datetime null ,
        cliente_updated_at	datetime null ,
        taller_id	bigint null ,
        taller_nombre	text null ,
        taller_direccion	text null,
        UNIQUE (id)
    );

    ## GUARDAMOS LOS DATOS QUE VAMOS A TRABAJAR EN LA BBDD
    INSERT IGNORE INTO Autogestores_Warehouse.temp_table_sigedyp
    SELECT 
    *
    FROM GarageTracer.ORDENES_SIGEDYP s
    WHERE YEAR(s.created_at) = Temporada_Input;


    ## UNA VEZ SELECCIONADO EL SET DE DATOS QUE VAMOS A TRABAJAR
    ## PROCEDEMOS A CREAR LOS CENTROS O TALLERES QUE NOS FALTAN EN AUTOGESTORES
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal,IdTipoCentro)
    SELECT 
        s.taller_nombre,
        s.taller_nombre ,
        1,
        9
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    WHERE NOT EXISTS 
    (
        SELECT 
            *
        FROM Autogestores_Warehouse.Centro c 
        WHERE 
            c.Codigo = s.taller_nombre 
    )
    GROUP BY s.taller_id;


    ## UNA VEZ CREADO LOS CENTROS, PROCEDEMOS A GENERAR LA LISTA DE CLIENTES
    INSERT IGNORE INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario, IdCiudad,Rut,Activo,FechaCreacion)
    SELECT
    s.cliente_nombres,
    s.cliente_email,
    RIGHT(s.cliente_telefono, 9) ,
    1 as IdCiudad,
    TRIM( REPLACE(s.cliente_rut, "." ,"") ),
    1,
    s.created_at 
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    WHERE NOT EXISTS 
    (
        SELECT 
            *	
        FROM Autogestores_Warehouse.Clientes c 
        WHERE 
            c.Rut = TRIM( REPLACE(s.cliente_rut, "." ,"") )
    ) ;

    ## UNA VEZ GENERADO LOS CLIENTES
    ## PROCEDEMOS A GUARDAR LAS ORDENES EN LOS SERVICIOS 
    INSERT IGNORE INTO Autogestores_Warehouse.Servicio ( IdCliente, IdTipoServicio , IdCentro , FechaServicio , IdPlataformaOrigen , IdUsuarioPropietario , CodigoServicio , NumeroTelefono , Correo ,Nombre ,Ciudad ,Comuna )
    SELECT 
    c.IdClientes,
    10 as IdTipoServicio,
    c2.IdCentro,
    s.created_at,
    5 as IdPlataformaOrigen,
    null as IdUsuarioPropietario,
    s.id as CodigoServicio,
    s.cliente_telefono,
    s.cliente_email,
    s.cliente_nombres,
    null as Ciudad,
    null as Comuna
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    JOIN Autogestores_Warehouse.Clientes c ON c.Rut = TRIM( REPLACE(s.cliente_rut, "." ,"") )
    JOIN Autogestores_Warehouse.Centro c2 ON c2.Codigo = s.taller_nombre ;


    ## UNA VEZ GENERADO LOS SERVICIOS, PROCEDEMOS A GUARDAR EN DETALLE LA INFORMACIÓN DEL SERVICIO
    INSERT IGNORE INTO Autogestores_Warehouse.ServicioGT(
        IdServicio,
        Marca,
        Modelo,
        Patente,
        CodigoMotor,
        NumeroChasis,
        Kilometraje,
        AnioModelo,
        ColorVehiculo,
        MotivoIngreso,
        Aseguradora 
    )
    SELECT 
        s2.IdServicio,
        s.vehiculo_marca,
        s.vehiculo_modelo,
        s.vehiculo_patente,
        s.vehiculo_motor,
        s.vehiculo_chasis,
        s.vehiculo_kilometraje,
        s.vehiculo_anio,
        s.vehiculo_color,
        s.motivo_pedido,
        s.Aseguradora
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    JOIN Autogestores_Warehouse.Servicio s2 ON s2.IdTipoServicio = 10 AND s2.CodigoServicio = s.id ;


    ## UNA VEZ INGRESADO LOS SERVICIOS, PROCEDEMOS A CREAR LOS VEHICULOS NO EXISTENTES EN LA BBDD
    INSERT INTO Autogestores_Warehouse.Vehiculos (
        Marca,
        Modelo ,
        Patente,
        NumeroChasis,
        FechaIngreso,
        Kilometraje,
        AñoModelo,
        Color 
    )
    SELECT 
        s.vehiculo_marca,
        s.vehiculo_modelo,
        s.vehiculo_patente,
        s.vehiculo_chasis,
        s.created_at,
        s.vehiculo_kilometraje,
        s.vehiculo_anio,
        s.vehiculo_color
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    WHERE NOT EXISTS 
    (
        SELECT 
            * 
        FROM Autogestores_Warehouse.Vehiculos v 
        WHERE v.NumeroChasis = TRIM(s.vehiculo_chasis )
        
    )
    GROUP BY s.vehiculo_chasis;

    ## UNA VEZ CREADO LOS VEHICULOS
    ## PROCEDEMOS A VINCULARLOS A LOS CLIENTES
    INSERT INTO Autogestores_Warehouse.VehiculoCliente (IdVehiculo,IdCliente)
    SELECT
        v.IdVehiculo ,
        c.IdClientes 
    FROM Autogestores_Warehouse.temp_table_sigedyp s
    JOIN Autogestores_Warehouse.Vehiculos v ON v.NumeroChasis = s.vehiculo_chasis
    JOIN Autogestores_Warehouse.Clientes c  ON c.Rut  = TRIM( REPLACE(s.cliente_rut, "." ,"") )
    WHERE NOT EXISTS (
        SELECT * FROM Autogestores_Warehouse.VehiculoCliente vc
        WHERE vc.IdVehiculo = v.IdVehiculo  AND vc.IdCliente = c.IdClientes 
    ) ;

    ## POR ULTIMO ELIMINAMOS LA TABLA TEMPORAL
    DROP TABLE Autogestores_Warehouse.temp_table_sigedyp ;




END
//
 