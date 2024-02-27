DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_AGREGAR_CREDITOS_AMICAR(
 	IN Temporada_Input int,
 	IN Periodo_Input int
 )
    BEGIN 

    ## -----------------------------------------------------------------------------------------------------------
    #### SELECCIONAMOS LOS DATOS DE LOS CLIENTES QUE HAN OPTADO A TOMAR O SOLICITAR UN CREDITO EN AMICAR 
    ## -----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal,IdTipoCentro)
    SELECT 
        TRIM(s.`Local`) as codigo,
        TRIM(s.`Local`) as centro,
        1,
        4
    FROM Amicar.AMICAR_CLIENTES_POR_ESTADO s
        WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Centro c WHERE c.Descripcion = TRIM(s.`Local`))
    GROUP BY s.`Local`;
    ## -----------------------------------------------------------------------------------------------------------


    ## -----------------------------------------------------------------------------------------------------------
    ## SELECCIONAMOS TODOS LOS CLIENTES UNICOS DE LOS CREDITOS EN AMICAR
    ## -----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo,FechaCreacion)
    SELECT 
    s.Nombre_Cliente,
    s.Email_Cliente,
    s.Tel_Part,
    s.Tel_Cel, 
    NULL as TelefonoOpcional,
    1, ## CIUDAD POR DEFECTO
    ## DEFINICIÓN DEL RUT 				DIVISOR 		DIGITO VERIFICADOR
    CONCAT( SUBSTRING(s.Rut, 1 , LENGTH(s.Rut) -1  ),  "-",  SUBSTRING(s.Rut, LENGTH(s.Rut) , LENGTH(s.Rut)  )  ) as Rut,
    CASE
        WHEN LENGTH(CONCAT( SUBSTRING(s.Rut, 1 , LENGTH(s.Rut) -1  ),  "-",  SUBSTRING(s.Rut, LENGTH(s.Rut) , LENGTH(s.Rut)  )  )) > 6 THEN 1 ELSE 0
    END as activo,
    IFNULL(s.F_Cot,CURRENT_TIMESTAMP()) as FechaCrecion
    FROM Amicar.AMICAR_CLIENTES_POR_ESTADO s
    WHERE NOT EXISTS (
        SELECT 
            *
        FROM Autogestores_Warehouse.Clientes c 
        WHERE 
            c.Rut  = CONCAT( SUBSTRING(s.Rut, 1 , LENGTH(s.Rut) -1  ),  "-",  SUBSTRING(s.Rut, LENGTH(s.Rut) , LENGTH(s.Rut)  ))
    )
    GROUP BY CONCAT( SUBSTRING(s.Rut, 1 , LENGTH(s.Rut) -1  ),  "-",  SUBSTRING(s.Rut, LENGTH(s.Rut) , LENGTH(s.Rut)  )  );
    ## -----------------------------------------------------------------------------------------------------------


    ## -----------------------------------------------------------------------------------------------------------
    ## GENERAMOS LA LISTA DE SERVICIOS
    ## -----------------------------------------------------------------------------------------------------------
    --  Auto-generated SQL script #202401231317
    INSERT INTO Autogestores_Warehouse.Servicio (IdCliente,IdTipoServicio,IdCentro,FechaServicio,IdPlataformaOrigen,IdUsuarioPropietario,CodigoServicio,NumeroTelefono,Correo,Nombre,Ciudad,Comuna)
    SELECT 
    c.IdClientes,
    6, #TipoServicio = Creditos Nuevos
    c2.IdCentro ,
    a.Fecha_Adjudicado,
    3, #PLATAFORMA ORIGEN = AMICAR
    null,  # USUARIO PROPIETARIO
    a.ID_Credito as CodigoServicio,
    null, # telefono
    null, # correo
    a.Nombre_Cliente,
    null, #ciudad,
    null #comuna
    FROM Amicar.AMICAR_ADJUDICADOS a
    JOIN Autogestores_Warehouse.Clientes c  ON  c.Rut = CONCAT( SUBSTRING(a.Rut_Cliente , 1 , LENGTH(a.Rut_Cliente) -1  ),  "-",  SUBSTRING(a.Rut_Cliente, LENGTH(a.Rut_Cliente) , LENGTH(a.Rut_Cliente)  ))
    JOIN Autogestores_Warehouse.Centro c2 ON c2.Codigo  = TRIM(a.`Local`) 
    WHERE NOT EXISTS 
    (
        SELECT 
            *
        FROM Autogestores_Warehouse.Servicio s 
        WHERE s.IdTipoServicio  = 6 AND s.IdPlataformaOrigen = 3 
        AND s.CodigoServicio = a.ID_Credito 
    ) AND YEAR(a.Fecha_Adjudicado) = Temporada_Input AND MONTH(a.Fecha_Adjudicado) = Periodo_Input 
    GROUP BY a.ID_Credito;

    ## -----------------------------------------------------------------------------------------------------------
    ## GUARDAMOS EL DETALLE DEL TIPO DE SERVICIO YA GENERADO EN LA BBDD
    ## -----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.ServicioCredito (IdServicio,Marca,Modelo,AñoModelo,NombreProducto,EstadoAuto,Precio,Pie,MontoPagare,Plazo,SaldoPrecio,TasaCurse,ValorCuota,Sueldo,TipoVehiculo,Canal,IdEstadoCredito,Vendedor,InstituciónFinanciera)
    SELECT 
        s.IdServicio,
        a.Marca,
        a.`Version` as modelo, 
        a.Año_Vehiculo,
        a.Nombre_Producto,
        "NUEVO" as Estado,
        a.Precio_Vehiculo,
        a.Pie,
        null, #MONTO PAGARE
        a.Plazo,
        a.Saldo,
        a.Tasa,
        null, #valor cuota
        null, #sueldo,
        "NUEVO" as Tipo,
        null as Canal,
        13, #Adjudiado
        a.Vendedor,
        a.Entidad_Financiera 
    FROM Amicar.AMICAR_ADJUDICADOS a
    JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = a.ID_Credito AND s.IdTipoServicio = 6 AND s.IdPlataformaOrigen = 3
    WHERE NOT EXISTS 
    (
        SELECT 
            *
        FROM Autogestores_Warehouse.ServicioCredito sc 
        WHERE sc.IdServicio = s.IdServicio 
    ) AND YEAR(a.Fecha_Adjudicado)  = Temporada_Input AND MONTH(a.Fecha_Adjudicado) = Periodo_Input
    GROUP BY a.ID_Credito ;
    ## -----------------------------------------------------------------------------------------------------------

















    ## -----------------------------------------------------------------------------------------------------------
    ## INGRESAMOS LOS APROBADOS NO ADJUCIDADOS NO INGRESADOS A LA BBDD
    ## -----------------------------------------------------------------------------------------------------------



    #### SELECCIONAMOS LOS DATOS DE LOS CLIENTES QUE HAN OPTADO A TOMAR O SOLICITAR UN CREDITO EN AMICAR 
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal,IdTipoCentro)
    SELECT 
        TRIM(s.Nombre_local) as codigo,
        TRIM(s.Nombre_local) as centro,
        1,
        4
    FROM Amicar.AMICAR  s
        WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Centro c WHERE c.Descripcion = TRIM(s.Nombre_local))
    GROUP BY s.Nombre_local;
    ## -----------------------------------------------------------------------------------------------------------




    ## -----------------------------------------------------------------------------------------------------------
    ## SELECCIONAMOS TODOS LOS CLIENTES UNICOS DE LOS CREDITOS EN AMICAR | APROBADOS NO ADJUDICADOS
    ## -----------------------------------------------------------------------------------------------------------
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo,FechaCreacion)
    SELECT 
    s.Nombre_Cliente,
    s.Email_Cliente,
    RIGHT (s.Fono_Celular,9)  ,
    RIGHT(s.Fono_Particular,9) ,
    RIGHT(s.Fono_Comercial,9) ,
    1, ## CIUDAD POR DEFECTO
    ## DEFINICIÓN DEL RUT 				DIVISOR 		DIGITO VERIFICADOR
    CONCAT( SUBSTRING(s.Rut_Cliente , 1 , LENGTH(s.Rut_Cliente) -1  ),  "-",  SUBSTRING(s.Rut_Cliente, LENGTH(s.Rut_Cliente) , LENGTH(s.Rut_Cliente)  )  ) as Rut,
    CASE
        WHEN LENGTH(CONCAT( SUBSTRING(s.Rut_Cliente, 1 , LENGTH(s.Rut_Cliente) -1  ),  "-",  SUBSTRING(s.Rut_Cliente, LENGTH(s.Rut_Cliente) , LENGTH(s.Rut_Cliente)  )  )) > 6 THEN 1 ELSE 0
    END as activo,
    IFNULL(s.Fecha_solicitud ,CURRENT_TIMESTAMP()) as FechaCrecion
    FROM Amicar.AMICAR s
    WHERE NOT EXISTS (
        SELECT 
            *
        FROM Autogestores_Warehouse.Clientes c 
        WHERE 
            c.Rut  = CONCAT( SUBSTRING(s.Rut_Cliente, 1 , LENGTH(s.Rut_Cliente) -1  ),  "-",  SUBSTRING(s.Rut_Cliente, LENGTH(s.Rut_Cliente) , LENGTH(s.Rut_Cliente)  ))
    ) AND YEAR(s.Fecha_solicitud) = Temporada_Input 
    GROUP BY CONCAT( SUBSTRING(s.Rut_Cliente , 1 , LENGTH(s.Rut_Cliente) -1  ),  "-",  SUBSTRING(s.Rut_Cliente, LENGTH(s.Rut_Cliente) , LENGTH(s.Rut_Cliente)  )  );
    ## ---


    ## -----------------------------------------------------------------------------------------------------------
    ## INGRESAMOS TODOS LOS SERVICIOS DE LOS APROBADOS NO ADJUDICADOS
    ## -----------------------------------------------------------------------------------------------------------
 

    ## GENERAMOS LA TABLA TEMPORAL QUE NOS PERMITE INGRESAR LOS DATOS COMO CORRESPONDE
    DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_aprobados_no_adjudicados;
    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_aprobados_no_adjudicados(
        IdCliente	int	NULL,
        IdTipoServicio	int	NULL,
        IdCentro	int	NULL,
        FechaServicio	datetime	NULL,
        IdPlataformaOrigen	int	NULL,
        IdUsuarioPropietario	int	NULL,
        CodigoServicio	varchar(100)	NULL,
        NumeroTelefono	varchar(100)	NULL,
        Correo	varchar(100)	NULL,
        Nombre	varchar(100)	NULL,
        Ciudad	varchar(255)	NULL,
        Comuna	varchar(255)	NULL
    ) ;

    ## ALMACENAMOS LOS DATOS A TRABAJAR
    INSERT INTO Autogestores_Warehouse.temp_lista_aprobados_no_adjudicados (IdCliente, IdTipoServicio, IdCentro, FechaServicio, IdPlataformaOrigen, IdUsuarioPropietario, CodigoServicio, NumeroTelefono, Correo, Nombre, Ciudad, Comuna)
    SELECT 
    c.IdClientes,
    6, #TipoServicio = Creditos Nuevos
    c2.IdCentro ,
    a.Fecha_solicitud,
    3, #PLATAFORMA ORIGEN = AMICAR
    null,  # USUARIO PROPIETARIO
    a.Id_Credito as CodigoServicio,
    null, # telefono
    null, # correo
    a.Nombre_Cliente,
    null, #ciudad,
    null #comuna
    FROM Amicar.AMICAR a
    JOIN Autogestores_Warehouse.Clientes c  ON  c.Rut = CONCAT( SUBSTRING(a.Rut_Cliente , 1 , LENGTH(a.Rut_Cliente) -1  ),  "-",  SUBSTRING(a.Rut_Cliente, LENGTH(a.Rut_Cliente) , LENGTH(a.Rut_Cliente)  ))
    JOIN Autogestores_Warehouse.Centro c2 ON c2.Codigo  = TRIM( a.Nombre_local  ) 
    WHERE 
        YEAR(a.Fecha_solicitud ) = Temporada_Input	
    GROUP BY a.Id_Credito ;


    ## PROCEDEMOS A GUARDAR LOS DATOS DE LOS CREDITOS APROBADOS NO ADJUDICADOS
    INSERT INTO Autogestores_Warehouse.Servicio (IdCliente, IdTipoServicio, IdCentro, FechaServicio, IdPlataformaOrigen, IdUsuarioPropietario, CodigoServicio, NumeroTelefono, Correo, Nombre, Ciudad, Comuna)
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
    FROM Autogestores_Warehouse.temp_lista_aprobados_no_adjudicados  a
    WHERE NOT EXISTS 
        (
            SELECT 
                *
            FROM Autogestores_Warehouse.Servicio s 
            WHERE s.IdTipoServicio  = 6 AND s.IdPlataformaOrigen = 3 
            AND s.CodigoServicio = a.CodigoServicio  
        )
    GROUP BY a.CodigoServicio ;


    

    

    
    INSERT INTO Autogestores_Warehouse.ServicioCredito (IdServicio,Marca,Modelo,AñoModelo,NombreProducto,EstadoAuto,Precio,Pie,MontoPagare,Plazo,SaldoPrecio,TasaCurse,ValorCuota,Sueldo,TipoVehiculo,Canal,IdEstadoCredito,Vendedor,InstituciónFinanciera)
    SELECT 
        s.IdServicio,
        a.Marca_vehiculo ,
        a.Modelo_Vehiculo  as modelo, 
        null , ## AÑO MODELO
        a.Nombre_Producto,
        "NUEVO" as Estado,
        NULL , #PRECIO VEHICULO
        a.Contado as Pie,
        a.Saldo , #MONTO PAGARE
        a.Plazo,
        a.Saldo,
        null as Tasa,
        null, #valor cuota
        null, #sueldo,
        "NUEVO" as Tipo,
        null as Canal,
        14, #Adjudiado
        a.Nombre_Vendedor ,
        null as NombreProducto
    FROM Amicar.AMICAR a
    JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = a.Id_Credito  AND s.IdTipoServicio = 6 AND s.IdPlataformaOrigen = 3
    WHERE NOT EXISTS 
    (
        SELECT 
            *
        FROM Autogestores_Warehouse.ServicioCredito sc 
        WHERE sc.IdServicio = s.IdServicio 
    )
    GROUP BY a.ID_Credito ;

END
//
    