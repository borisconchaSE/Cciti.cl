DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_GUARDAR_DATOS_VENTA_MESON(
    IN Temporada_Input int,
    IN Periodo_Input int 
)
BEGIN 

    ## -----------------------------------------------------------------------------------
    ## EL PRIMARY KEY DE VENTA MESON SE DEFINE DE LA SIGUIENTE MANERA:
    ## SE DEBE REALIZAR UN CONCAT CON LOS SIGUIENTES CAMPOS
    ##  -   CODIGO
    ##  -   REFERENCIA
    ##  -   NUMERO DE DOCUMENTO
    ##  
    ##  EL METODO DE SER UTILIZADO ES EL SIGUIENTE:
    ##  CONCAT(s.Codigo, s.Referencia, s.N_Documento  )
    ## -----------------------------------------------------------------------------------



    ## -----------------------------------------------------------------------------------
    ## EN PRIMER LUGAR, EXTRAEMOS LOS CENTROS DE LA TRANSACCIÓN
    ## -----------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal,IdTipoCentro)
    SELECT
        s.Bodega,
        s.Bodega,
        1,
        7
    FROM prodDatafile_dev.SIGA_DETALLE_VENTA_MESON_DETALLE s
    WHERE NOT EXISTS (
        SELECT 
            *
        FROM Autogestores_Warehouse.Centro c WHERE c.Codigo  = s.Bodega 
    )
    GROUP BY s.Bodega;
    ## -----------------------------------------------------------------------------------



    ## -----------------------------------------------------------------------------------
    ## UNA VEZ INGRESADO LOS CENTRO, PROCEDEMOS A GUARDAR LA LISTA DE CLIENTES
    ## -----------------------------------------------------------------------------------
    INSERT IGNORE INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo,FechaCreacion)
    SELECT 
    s.Nombre_Cliente,
    null as CorreoCliente,
    null as TelefonoPrimario,
    null as TelefonoSecundario,
    null as TelefonoOpcional,
    1 as IdCiudad,
    REPLACE( s.RUT_Cliente, ".", "" ) as RUT,
    CASE WHEN LENGTH(REPLACE( s.RUT_Cliente, ".", "" )) > 5 THEN 1 ELSE 0 END as Activo,
    s.Fecha 
    FROM prodDatafile_dev.SIGA_DETALLE_VENTA_MESON_DETALLE s
    WHERE NOT EXISTS (
        SELECT 
            *
        FROM Autogestores_Warehouse.Clientes c 
        WHERE 
            c.Rut  = REPLACE( s.RUT_Cliente, ".", "" )
    ) AND s.Temporada = Temporada_Input AND s.Periodo = Periodo_Input
    GROUP BY REPLACE( s.RUT_Cliente, ".", "" );

    ## -----------------------------------------------------------------------------------
    ## UNA VEZ GENERADO LA LISTA DE CLIENTES, PROCEDEMOS A GENERAR LA LISTA DE SERIVICIOS
    ## -----------------------------------------------------------------------------------
    INSERT IGNORE INTO Autogestores_Warehouse.Servicio (IdCliente,IdTipoServicio,IdCentro,FechaServicio,IdPlataformaOrigen,IdUsuarioPropietario,CodigoServicio,NumeroTelefono,Correo,Nombre,Ciudad,Comuna)
    SELECT 
        c.IdClientes					as IdCliente,
        8 								as IdTipoServicio,
        c2.IdCentro 					as IdCentro,
        s.Fecha_Referencia 				as FechaServicio,
        2								as IdPlataformaOrigen,
        null 							as IdUsuarioPropietario,
        CONCAT(s.Codigo, s.Referencia, s.N_Documento  ) as CodigoSErvicio,
        null 							as NumeroTelefono,
        null 							as Correo,
        s.Nombre_Cliente 				as Nombre,
        null 							as Ciudad,
        null 							as Comuna
    FROM prodDatafile_dev.SIGA_DETALLE_VENTA_MESON_DETALLE s
    JOIN Autogestores_Warehouse.Clientes 	c 	ON c.Rut  = REPLACE( s.RUT_Cliente, ".", "" ) 
    JOIN Autogestores_Warehouse.Centro 		c2 	ON c2.Codigo = s.Bodega 
    WHERE s.Temporada = Temporada_Input AND s.Periodo = Periodo_Input
    GROUP BY CONCAT(s.Codigo, s.Referencia, s.N_Documento  );




    ## PARA PODER COMENZAR A TRABAJAR CON LA INFORMACIÓN DEL DETALLE DE VENTA MESON
    ## GUARDAMOS LA INFORMACIÓN EN UNA TABLA TEMPORAL PARA FACILITAR EL CRUCE DE INFORMACIÓN
    ## E IDENTIFICACIÓN DE LOS CAMPOS
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_venta_meson_detalle;

    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_venta_meson_detalle (
        Codigo	int NULL,
        IdServicio	int NULL,
        Vendedor	varchar(255) NULL,
        NumeroDocumento	varchar(125) NULL,
        Fecha	datetime NULL,
        FechaReferencia	datetime NULL,
        Cantidad	int NULL,
        TotalVenta	int NULL,
        Descuentos	int NULL,
        TotalCobrado	int NULL,
        Costo	int NULL,
        Margen	int NULL,
        Porcentaje	float NULL,
        Familia	varchar(125) NULL,
        Clasificacion	varchar(125) NULL,
        Marca	varchar(255) NULL,
        Categoria	varchar(255) NULL,
        NumeroCotizacion	int NULL,
        CV	int NULL,
        NumeroParte	varchar(125) NULL,
        CodigoUnico	varchar(100) NULL
    ) ;


    INSERT INTO Autogestores_Warehouse.temp_venta_meson_detalle
    SELECT 
    s.Codigo ,
    null as IdServicio,
    s.Vendedor as Vendedor,
    s.N_Documento,
    s.Fecha,
    s.Fecha_Referencia ,
    s.Cantidad ,
    s.Total_Venta,
    s.Descuentos,
    s.Total_Cobrado,
    s.Costo,
    s.Margen ,
    s.Porcentaje ,
    s.Familia,
    s.Clasificacion,
    s.Marca ,
    s.Categoria,
    s.N_Cotizacion,
    s.C_V,
    s.N_Parte,
    CONCAT(s.Codigo, s.Referencia, s.N_Documento  ) as Codigo
    FROM prodDatafile_dev.SIGA_DETALLE_VENTA_MESON_DETALLE s
    WHERE s.Temporada = Temporada_Input AND s.Periodo = Periodo_Input
    GROUP BY CONCAT(s.Codigo, s.Referencia, s.N_Documento  );


    INSERT IGNORE INTO Autogestores_Warehouse.ServicioVentaMeson (Codigo,IdServicio,Vendedor,NumeroDocumento,Fecha,FechaReferencia,Cantidad,TotalVenta,Descuentos,TotalCobrado,Costo,Margen,Porcentaje,Familia,Clasificacion,Marca,Categoria,CV,NumeroParte, CodigoUnico)
    SELECT  
    s.Codigo,
    s2.IdServicio ,
    s.Vendedor,
    s.NumeroDocumento,
    s.Fecha,
    s.FechaReferencia,
    s.Cantidad,
    s.TotalVenta,
    s.Descuentos,
    s.TotalCobrado,
    s.Costo,
    s.Margen,
    s.Porcentaje,
    s.Familia,
    s.Clasificacion,
    s.Marca,
    s.Categoria,
    s.CV,
    s.NumeroParte,
    CodigoUnico
    FROM Autogestores_Warehouse.temp_venta_meson_detalle s
    JOIN Autogestores_Warehouse.Servicio s2 ON s2.IdTipoServicio = 8 AND s2.CodigoServicio = s.CodigoUnico;

    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_venta_meson_detalle;


END
//