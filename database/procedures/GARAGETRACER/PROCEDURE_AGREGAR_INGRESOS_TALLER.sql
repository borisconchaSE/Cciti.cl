
DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_CARGAR_DATOS_GARAGETRACER(
    IN Temporada_Input int 
)
BEGIN 
## -----------------------------------------------------------------------------------
## INICIAMOS EL PROCESO DE INGRESAR LAS ORDENES GENERADAS EN GARAGETRACER
## -----------------------------------------------------------------------------------

## PROCEDEMOS A INGRESAR LOS TALLERES DE GT AL SISTEMA DE AUTOGESTORES
INSERT INTO Autogestores_Warehouse.Centro (IdSucursal, Codigo,Descripcion,IdTipoCentro)
SELECT 
1 as IdSucursal ,
CONCAT("Taller", og.taller_nombre) as Codigo,
og.taller_nombre,
6 as IdTipoCentro
FROM GarageTracer.ORDENES_GT og
WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Centro c WHERE c.Codigo = CONCAT("TALLER", og.taller_nombre))
GROUP BY og.taller_id;


## INGRESAMOS LOS CLIENTES NO EXISTENTES EN LA BBDD DE AUTOGESTORES
INSERT INTO Autogestores_Warehouse.Clientes (NombreCliente,CorreoCliente,TelefonoPrimario,TelefonoSecundario,TelefonoOpcional,IdCiudad,Rut,Activo,FechaCreacion)
SELECT 
	UPPER(CONCAT(	og.cliente_nombres," ",og.cliente_apellidos))  as NombreCliente,
	og.cliente_email,
	RIGHT(og.cliente_telefono, 9)  as TelefonoPrimario,
	null 				as TelefonoSecundario,
	null 				as TelefonoOpcional,
	1 as ClienteCiudad,
	REPLACE(og.cliente_rut,".","") as Rut,
	CASE WHEN LENGTH(REPLACE(og.cliente_rut,".","")) > 5 THEN 1 ELSE 0 END as Activo,
	og.created_at 
FROM GarageTracer.ORDENES_GT og
WHERE og.cliente_id NOT IN (28819,2,18717,27330,1,28818) AND 
	NOT EXISTS (
	SELECT 
		*
	FROM Autogestores_Warehouse.Clientes c WHERE c.Rut = REPLACE(og.cliente_rut,".","")
) AND YEAR(og.created_at) = Temporada_Input
GROUP BY REPLACE(og.cliente_rut,".","");

DROP TABLE IF EXISTS  Autogestores_Warehouse.temp_lista_GT_servicios;

CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_GT_servicios(
    IdServicio	int NULL,
    IdCliente	int NULL,
    IdTipoServicio	int NULL,
    IdCentro	int NULL,
    FechaServicio	datetime    NULL,
    IdPlataformaOrigen	int NULL,
    IdUsuarioPropietario	int NULL,
    CodigoServicio	varchar(100)    NULL,
    NumeroTelefono	varchar(100)    NULL,
    Correo	varchar(100)    NULL,
    Nombre	varchar(255)    NULL,
    Ciudad	varchar(255)    NULL,
    Comuna	varchar(255)    NULL
);


INSERT INTO Autogestores_Warehouse.temp_lista_GT_servicios (
IdServicio,
IdCliente,
IdTipoServicio,
IdCentro,
FechaServicio,
IdPlataformaOrigen,
IdUsuarioPropietario ,
CodigoServicio,
NumeroTelefono,
Correo,
Nombre,
Ciudad,
Comuna
)
SELECT 
og.id as IdServicio,
c.IdClientes,
3 				as IdTipoServicio,
c2.IdCentro,
og.created_at 	as FechaServicio,
5 		as IdPlataformaOrigen,
null as IdUsuarioPropietario,
og.id 	as CodigoServicio,
og.cliente_telefono as NumeroTelefono,
og.cliente_email as Correo,
UPPER(CONCAT(	og.cliente_nombres," ",og.cliente_apellidos))  as Nombre,
og.cliente_comuna as Ciudad, 
og.cliente_comuna 
FROM GarageTracer.ORDENES_GT og
JOIN Autogestores_Warehouse.Clientes c 	ON c.Rut  		= REPLACE(og.cliente_rut,".","")
JOIN Autogestores_Warehouse.Centro c2 	ON c2.Codigo 	= CONCAT("TALLER", og.taller_nombre)
WHERE YEAR(og.created_at) = Temporada_Input
GROUP BY og.id;



## INSERTAMOS EN LA BBDD LOS NUEVOS VALORES
INSERT IGNORE INTO Autogestores_Warehouse.Servicio  (
IdCliente,
IdTipoServicio,
IdCentro,
FechaServicio,
IdPlataformaOrigen,
IdUsuarioPropietario ,
CodigoServicio,
NumeroTelefono,
Correo,
Nombre,
Ciudad,
Comuna
)
SELECT
og.IdCliente,
og.IdTipoServicio,
og.IdCentro,
og.FechaServicio,
og.IdPlataformaOrigen,
og.IdUsuarioPropietario ,
og.CodigoServicio,
og.NumeroTelefono,
og.Correo,
og.Nombre,
og.Ciudad,
og.Comuna
FROM Autogestores_Warehouse.temp_lista_GT_servicios og;

## ELIMINAMOS LA TABLA TEMPORAL
DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_GT_servicios;



## INGRESAMOS EL DETALLE DE LAS ORDENES ENCONTRADAS EN EL SISTEMA
INSERT IGNORE INTO Autogestores_Warehouse.ServicioGT (
IdServicio,
Marca ,
Modelo ,
Patente ,
CodigoMotor ,
NumeroChasis ,
Kilometraje ,
AnioModelo ,
ColorVehiculo ,
MotivoIngreso 
)
SELECT 
s.IdServicio,
og.vehiculo_marca,
og.vehiculo_modelo,
og.vehiculo_patente,
og.vehiculo_motor,
og.vehiculo_chasis,
og.vehiculo_kilometraje,
og.vehiculo_anio,
og.vehiculo_color,
og.requerimiento_nombre
FROM GarageTracer.ORDENES_GT og 
JOIN Autogestores_Warehouse.Servicio s ON s.CodigoServicio = og.id  AND s.IdTipoServicio = 3
WHERE YEAR(og.created_at)  = Temporada_Input
GROUP BY s.IdServicio ;


END
//