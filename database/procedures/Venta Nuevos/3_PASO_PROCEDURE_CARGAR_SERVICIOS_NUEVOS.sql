DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_CARGAR_SERVICIOS_NUEVOS(
 	IN Temporada_Input int ,
    IN Periodo_Input int 
 )
BEGIN 
    ## --------------------------------------------------------------------------------------------
    ## GENERAMOSA EL PROCEDIMIENTO PARA GENERAR LOS SERVICIOS EN LA BBDD DE AUTOGESTORES
    ## --------------------------------------------------------------------------------------------


    ## GENERAMOS LOS CENTROS PARA VINVULAR CORRECTAMENTE LOS DATOS A A BBDD
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal)
    SELECT
        trim(sb.id_centro),
        sb.centro ,
        1
    FROM prodDatafile_dev.SAP_BEX1 sb
        WHERE sb.id_centro IN (
            "C107",
            "C139",
            "C155",
            "C009",
            "C007",
            "C108",
            "C010",
            "C011",
            "C012"
        ) 
        AND NOT EXISTS (
            SELECT 
                *
            FROM Autogestores_Warehouse.Centro c 
            WHERE 
                c.Codigo = TRIM(sb.id_centro)
        )
        AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
    GROUP BY sb.id_centro
    UNION ALL
    SELECT
        trim(sb.id_centro),
        sb.centro ,
        1
    FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO sb 
        WHERE NOT EXISTS (
            SELECT 
                *
            FROM Autogestores_Warehouse.Centro c 
            WHERE 
                c.Codigo = TRIM(sb.id_centro)
        )
        AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
    GROUP BY sb.id_centro;



    ## GENERAMOS UNA TABLA TEMPORAL PARA FACILITAR LA CARGA DE LOS CLIENTES A LA BBDD DE AUTOGESTORES
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_servicios;
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_servicios(
        rut varchar(255) not null,
        centro varchar(255) not null,
        Fecha DATETIME not null,
        pedido varchar(255) null,
        factura_sap varchar(255) null,
        NumeroTelefono varchar(255) null,
        Correo varchar(255) null,
        Nombre varchar(255) null
    );

    ## llenamos la tabla temporal de los datos que vamos a trabajar
    INSERT INTO Autogestores_Warehouse.temp_lista_servicios (rut,centro,Fecha,pedido,factura_sap,NumeroTelefono,Correo,Nombre  )
    SELECT 
        sb.numero_id_fiscal,
        sb.id_centro,
        sb.dia_natural,
        sb.pedido,
        sb.factura_sap,
        sb.telefono_primario,
        sb.email,
        sb.cliente 
    FROM prodDatafile_dev.SAP_BEX1 sb
    WHERE sb.marca_derco IS NOT NULL AND sb.modelo IS NOT NULL AND sb.numero_chasis IS NOT NULL  
    AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
    AND  sb.id_centro IN (
        "C107",
        "C139",
        "C155",
        "C009",
        "C007",
        "C108",
        "C010",
        "C011",
        "C012"
    )
    GROUP BY sb.numero_chasis 
    UNION ALL
    SELECT 
        sb2.numero_id_fiscal,
        sb2.id_centro,
        sb2.dia_natural,
        sb2.pedido,
        sb2.numero_chasis,
        sb2.telefono_1 ,
        sb2.correo,
        sb2.nombre_cliente 
    FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO sb2
    WHERE sb2.marca_derco IS NOT NULL AND sb2.modelo IS NOT NULL AND sb2.numero_chasis IS NOT NULL  
    AND YEAR(sb2.dia_natural) = Temporada_Input AND MONTH(sb2.dia_natural) = Periodo_Input
    GROUP BY sb2.numero_chasis;


    ## GENERAMOS TODOS LOS NEGOCIOS NO EXISTENTES EN LA BBDD DE AUTOGESTORES
    INSERT INTO Autogestores_Warehouse.Servicio (IdCliente,IdTipoServicio,IdCentro,FechaServicio,IdPlataformaOrigen,IdUsuarioPropietario,CodigoServicio, NumeroTelefono,Correo,Nombre)
    SELECT 
    c.IdClientes,	## KEY DEL CLIENTE
    1 ,				## VENTA NUEVOS
    c2.IdCentro, 	## ID DEL CENTRO
    s.Fecha,		## FECHA FACURA
    1,				## ORIGEN SAP,
    null, 			## PROPIETARIO
    CONCAT(s.pedido,"-",s.factura_sap),
    s.NumeroTelefono,
    s.Correo,
    s.Nombre
    FROM Autogestores_Warehouse.temp_lista_servicios s
    JOIN Autogestores_Warehouse.Clientes c 	ON c.Rut 		= 	TRIM(REPLACE(s.rut,".",""))
    JOIN Autogestores_Warehouse.Centro c2 	ON c2.Codigo 	=	s.centro
    WHERE NOT EXISTS (
        SELECT 
            *
        FROM Autogestores_Warehouse.Servicio s2 
        WHERE 
            s2.IdCliente 		= 	c.IdClientes AND
            s2.IdCentro  		= 	c2.IdCentro  AND 
            s2.CodigoServicio 	=	CONCAT(s.pedido,"-",s.factura_sap) 
    ) ;

    ## ELIMINAMOS LA TABLA TEMPORAL QUE NOS ESTARIA UTILIZANDO ESPACIO EN MEMORIA
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_servicios;






    ## UNA VEZ GENERADA LA FACTURACIÓN EN LA BBDD PROCEDEMOS A INCORPORAR EL DETALLE - ESTO CORRESPONDE A LA INFORMACIÓN DE SEM
    INSERT INTO Autogestores_Warehouse.ServicioVenta (IdServicio,ClasePedido,Marca,Modelo,Patente,Chasis,Color,NumeroPedido,FechaPedido,NumeroFactura,FechaFactura,TipoPedido,AñoModelo,IndCompraPara,DPS,DPE,PrecioLista,PrecioCompra,Descuentos,Accesorios,PrecioVenta,CargosVehiculo,GastosProvicionados,Margen,NumeroStock,CodigoVehiculo,FechaRecepcion,AutorizadoPor,InstitucionCredito,ValorCredito,MarcaRetoma,ModeloRetoma,AñoRetoma,ValorRetoma,CodigoRetoma,PatenteRetoma,IdVehiculo,MundoVenta)
    SELECT 
    s.IdServicio,
    sb.clase_pedido,
    sb.marca_derco,
    sb.modelo,
    sb.matricula,
    sb.numero_chasis,
    sb.descripcion_color,
    sb.pedido,
    sb.dia_natural,
    sb.factura_sap,
    sb.dia_natural,
    sb.tipo_financiamiento ,
    sb.anio_modelo_vehiculo,
    0,
    null,
    null,
    SUM( REPLACE(sb.precio_lista_clp,".","") ) * 1,
    null, ## precio de compra
    REPLACE(sb.descuento_total_clp ,".","") * 1, ## descuentos,
    0, ## accesorios
    null, ## precio venta
    null, ## cargos
    null ,  ## gastos
    null, ## MARGEN
    sb.id_modelo, ## numero stock
    sb.numero_chasis, ## codigo modelo,
    sb.dia_natural,
    sb.cod_nombre_vendedor,
    null,
    null, 	## valor credito 
    null, 	## marca repoma
    null, 	##modelo retoma
    null, 	## año retoma
    null, 	## valor retoma
    null, 	## codgio retoma
    null, 	## patente retoma
    null,	## id vehiculo
    "NUEVOS" as MundoVenta 
    FROM prodDatafile_dev.SAP_BEX1 sb 
    JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = CONCAT(sb.pedido,"-",sb.factura_sap)
    WHERE sb.marca_derco IS NOT NULL AND sb.modelo IS NOT NULL AND sb.numero_chasis IS NOT NULL  
    AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
    AND  sb.id_centro IN (
        "C107",
        "C139",
        "C155",
        "C009",
        "C007",
        "C108",
        "C010",
        "C011",
        "C012"
    ) 
    AND NOT EXISTS (
        SELECT * 
        FROM Autogestores_Warehouse.ServicioVenta sv 
        WHERE 
            sv.IdServicio 		= s.IdServicio AND 
            sv.NumeroPedido 	= sb.pedido AND 
            sv.NumeroFactura 	= sb.factura_sap
        )
    GROUP BY sb.numero_chasis;








    ## UNA VEZ INSERTADO EL DETALLE DE LOS PEDIDOS DE SEM, PROCEDEMOS A INSERTAR LOS PEDIDOS DE SPA
    INSERT INTO Autogestores_Warehouse.ServicioVenta (IdServicio,ClasePedido,Marca,Modelo,Patente,Chasis,Color,NumeroPedido,FechaPedido,NumeroFactura,FechaFactura,TipoPedido,AñoModelo,IndCompraPara,DPS,DPE,PrecioLista,PrecioCompra,Descuentos,Accesorios,PrecioVenta,CargosVehiculo,GastosProvicionados,Margen,NumeroStock,CodigoVehiculo,FechaRecepcion,AutorizadoPor,InstitucionCredito,ValorCredito,MarcaRetoma,ModeloRetoma,AñoRetoma,ValorRetoma,CodigoRetoma,PatenteRetoma,IdVehiculo,MundoVenta)
    SELECT 
    s.IdServicio,
    sb.`clase Pedido` ,
    sb.marca_derco,
    sb.modelo,
    sb.matricula,
    sb.numero_chasis,
    sb.Color ,
    sb.pedido,
    sb.dia_natural,
    sb.numero_chasis ,
    sb.dia_natural,
    sb.entidad_financiera  ,
    null,
    0,
    null,
    null,
    SUM( REPLACE(sb.precio_lista_clp,".","") ) * 1,
    null, ## precio de compra
    REPLACE(sb.descuento_total_clp ,".","") * 1, ## descuentos,
    0, ## accesorios
    null, ## precio venta
    null, ## cargos
    null ,  ## gastos
    null, ## MARGEN
    sb.numero_modelo , ## numero stock
    sb.numero_chasis, ## codigo modelo,
    sb.dia_natural,
    sb.vendedor ,
    null,
    null, 	## valor credito 
    null, 	## marca repoma
    null, 	##modelo retoma
    null, 	## año retoma
    null, 	## valor retoma
    null, 	## codgio retoma
    null, 	## patente retoma
    null,	## id vehiculo
    "NUEVOS" as MundoVenta 
    FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO sb 
    JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = CONCAT(sb.pedido,"-",sb.numero_chasis)
    WHERE sb.marca_derco IS NOT NULL AND sb.modelo IS NOT NULL AND sb.numero_chasis IS NOT NULL  
    AND NOT EXISTS (
        SELECT * 
        FROM Autogestores_Warehouse.ServicioVenta sv 
        WHERE 
            sv.IdServicio 		= s.IdServicio AND 
            sv.NumeroPedido 	= sb.pedido AND 
            sv.NumeroFactura 	= sb.numero_chasis 
        )
    GROUP BY sb.numero_chasis;

END
//
