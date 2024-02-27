DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_CONTROL_EMISION(
    IN Temporada_Input int,
    IN Periodo_Input int
)
BEGIN 
    ## --------------------------------------------------------------------------------------------------------
    ## GENERAMOS EL PROCEDIMIENTO ALMACENADO QUE VA A PERMITIR INGRESAR LA INFORMACIÓN DE CONTROL DE EMISIÓN
    ## --------------------------------------------------------------------------------------------------------

    CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_control_emision_table(
        Clave	text null,
        Tipo	text null,
        N	bigint null,
        Fecha	datetime null,
        EnvSII	bigint null,
        RecSII	tinyint(1) null,
        AcuRec	tinyint(1) null,
        AcuCom	double null,
        AceptaReclamo	text null,
        Emitidaen	text null,
        Departamento	text null,
        RUT	text null,
        Cliente	text null,
        OrdendeCompra	text null,
        Neto	bigint null,
        Exento	bigint null,
        IVA	bigint null,
        IVAFueradePlazo	bigint null,
        OtrosImpuestos	bigint null,
        Total	bigint null,
        NCRescil	bigint null,
        NCFPlazo	bigint null,
        NCAdmin	bigint null,
        TipoNC	double null,
        Caja	text null,
        FechaCaja	datetime null,
        TipoEmision	bigint null,
        Referencia	text null,
        ReferenciaReEmision	double null,
        Concepto	text null,
        Usuario	text null,
        FechaDigitacion	datetime null,
        Hora	text null,
        Turno	bigint null,
        FormadePago	text null,
        SEA	double null,
        Origen	text null,
        Temporada	bigint null,
        Periodo	bigint null
    );

    INSERT INTO Autogestores_Warehouse.temp_control_emision_table
    SELECT * FROM prodDatafile_dev.SIGA_CONTROL_EMISION_DETALLE s
    WHERE YEAR(s.Fecha) = Temporada_Input AND MONTH(s.Fecha) = Periodo_Input;






    ## SE AGREGAN LOS CENTROS QUE NO SE ENCUENTRAN EN LA BBDD DE AUTOGESTORES
    INSERT INTO Autogestores_Warehouse.Centro (Codigo,Descripcion,IdSucursal,IdTipoCentro)
    SELECT 
        s.Departamento,
        s.Departamento,
        1,
        8
    FROM Autogestores_Warehouse.temp_control_emision_table s
    WHERE NOT EXISTS (
        SELECT * FROM Autogestores_Warehouse.Centro c WHERE c.Codigo = TRIM(s.Departamento)
    )
    GROUP BY s.Departamento;



    ## GENERAMOS LOS CLIENTES NO REGISTRADOS EN CONTROL DE EMISIÓN
    INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,Rut,Activo,FechaCreacion)
    SELECT
        s.Cliente,	
        TRIM( REPLACE(s.RUT,".","") ) as RUT,
        1,
        s.Fecha
    FROM Autogestores_Warehouse.temp_control_emision_table s
    WHERE NOT EXISTS (
        SELECT * FROM Autogestores_Warehouse.Clientes c WHERE c.Rut = TRIM( REPLACE(s.RUT,".","") )
    )
    GROUP BY s.RUT;



    ## INGRESAMOS LOS SERVICIOS ALMACENADOS DENTRO DE LA BBDD
    --  Auto-generated SQL script #202401251001
    INSERT IGNORE INTO Autogestores_Warehouse.Servicio (IdCliente,IdTipoServicio,IdCentro,FechaServicio,IdPlataformaOrigen,IdUsuarioPropietario,CodigoServicio,NumeroTelefono,Correo,Nombre,Ciudad,Comuna)
    SELECT
    c.IdClientes,
    9 as TipoServicio, 		## CONTROL DE EMSIÓN
    c2.IdCentro,
    s.Fecha,
    2 as PlataformaOrigen, 		## Plataforma Origen : SIGA
    null as IdUsuarioPropietario,
    s.Clave as CodigoServicio,
    null 	as NumeroTelefono,
    null 	as Correo,
    null 	as Nombre,
    null 	as Ciudad,
    null 	as Comuna
    FROM Autogestores_Warehouse.temp_control_emision_table s
    JOIN Autogestores_Warehouse.Clientes c ON c.Rut = TRIM( REPLACE(s.RUT,".","") )
    JOIN Autogestores_Warehouse.Centro c2 ON  c2.Codigo  = TRIM(s.Departamento);






    ## UNA VEZ INGRESADO LOS SERVICIOS 
    INSERT IGNORE INTO Autogestores_Warehouse.ServicioControlEmision (IdServicio,Numero,EnviaSII,RecSII,AcuRec,AceptaReclamo,EmitidaEn,Departamento,Cliente,OrdenDeCommpra,Neto,Exento,IVA,IvaFueraDePlazo,OtrosImpuestos,Total,NcRescil,NFCPlazo,NCAdmin,Caja,FechaCaja,TipoEmision,Referencia,Usuario,FechaDigitacion,Origen)
    SELECT 
    s2.IdServicio,
    s.Clave,
    s.EnvSII,
    s.RecSII,
    s.AcuRec,
    s.AceptaReclamo,
    s.Emitidaen,
    s.Departamento,
    s.Cliente,
    s.OrdendeCompra,
    s.Neto,
    s.Exento,
    s.IVA,
    s.IVAFueradePlazo,
    s.OtrosImpuestos,
    s.Total,
    s.NCRescil,
    s.NCFPlazo,
    s.NCAdmin ,
    s.Caja,
    s.FechaCaja,
    s.TipoEmision,
    s.Referencia,
    s.Usuario,
    s.FechaDigitacion,
    s.Origen 
    FROM prodDatafile_dev.SIGA_CONTROL_EMISION_DETALLE  s
    JOIN Autogestores_Warehouse.Servicio s2 ON s2.CodigoServicio = s.Clave;




END
//