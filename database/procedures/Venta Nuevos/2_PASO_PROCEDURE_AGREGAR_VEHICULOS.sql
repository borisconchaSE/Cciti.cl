DELIMITER //
CREATE PROCEDURE Autogestores_Warehouse.PROCEDURE_VINCULAR_UNIDADES_NUEVOS(
 	IN Temporada_Input int,
 	IN Periodo_Input 	int
 )
BEGIN 


DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos;

## CREAMOS UNA TABLA TEMPORAL  QUE NOS FACILITE Y OPTIMICE LA CREACIÓN DE VEHICULOS
CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos(
	marca VARCHAR(255) NOT NULL,
	modelo VARCHAR(255) NOT NULL,
	matricula VARCHAR(255) NULL,
	numero_chasis VARCHAR(255) NOT NULL,
	dia_natural DATETIME NOT NULL,
	kilometraje INT NOT NULL,
	año INT NULL
);



## EXTRAMOS TODOS LOS VEHICULOS EXISTENTES EN SAP SEM
INSERT IGNORE INTO Autogestores_Warehouse.temp_lista_clientes_vehiculos (marca, modelo, matricula, numero_chasis,dia_natural,kilometraje,año)
SELECT
	sb.marca_derco as Marca,
	sb.modelo,
	sb.matricula,
	sb.numero_chasis ,
	sb.dia_natural,
	0,
	sb.anio_modelo_vehiculo
FROM prodDatafile_dev.SAP_BEX1 sb
	WHERE sb.id_centro IN (
        "C007",
        "C009",
        "C010",
        "C012",
        "C108",
        "C155",
        "C011"
    ) 
    AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
	AND sb.marca_derco IS NOT NULL AND sb.modelo IS NOT NULL AND sb.numero_chasis IS NOT NULL 
GROUP BY sb.numero_chasis  
UNION ALL
SELECT
	sb2.marca_derco  as Marca,
	sb2.modelo,
	sb2.matricula,
	sb2.numero_chasis ,
	sb2.dia_natural,
	0,
	null
FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO sb2
	WHERE  
    YEAR(sb2.dia_natural) = Temporada_Input  AND MONTH(sb2.dia_natural) = Periodo_Input
    AND 
	sb2.marca_derco IS NOT NULL AND sb2.modelo IS NOT NULL AND sb2.numero_chasis IS NOT NULL 
GROUP BY sb2.numero_chasis   ;



## AGREGAMOS TODOS LOS VEHICULOS NUEVOS EN LA BBDD QUE NO EXISTAN EN LA BBDD 
INSERT IGNORE INTO Autogestores_Warehouse.Vehiculos (Marca,Modelo,Patente,NumeroChasis,FechaIngreso,Kilometraje,AñoModelo)
SELECT * 
FROM Autogestores_Warehouse.temp_lista_clientes_vehiculos s
WHERE NOT EXISTS (SELECT * FROM Autogestores_Warehouse.Vehiculos v WHERE v.NumeroChasis = s.numero_chasis );

## BORRAMOS LA TABLA TEMPORAL QUE YA NO VAMOS A UTILIZAR
DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos;



## ------------------------------------------------------------------------------------------
## GENERAMOS EL FLUJO QUE NOS PERMITA VINCULAR TODAS LAS UNIDADES A CADA UNO DE LOS CLIENTES
## ------------------------------------------------------------------------------------------


## CREAMOS LA TABLA TEMPORAL QUE ALMACENARÁ TODAS LAS VINCULACIONES DE LOS VEHICULOS CON LA INFORMACIÓN
## DE LOS CLIENTES

DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos_vinculantes;

CREATE TEMPORARY TABLE IF NOT EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos_vinculantes(
	rut VARCHAR(255) NOT NULL,
	numero_chasis VARCHAR(255) NOT NULL
);



## BUSCAMOS TODAS LAS UNIDADES CON CADA UNO DE SUS DUEÑOS
INSERT IGNORE INTO Autogestores_Warehouse.temp_lista_clientes_vehiculos_vinculantes (rut,numero_chasis)
 SELECT
	sb.numero_id_fiscal as rut,
	sb.numero_chasis 
FROM prodDatafile_dev.SAP_BEX1 sb
	WHERE sb.id_centro IN (
        "C007",
        "C009",
        "C010",
        "C012",
        "C108",
        "C155",
        "C011"
    ) 
    AND YEAR(sb.dia_natural) = Temporada_Input AND MONTH(sb.dia_natural) = Periodo_Input
	AND sb.marca_derco IS NOT NULL AND sb.modelo IS NOT NULL AND sb.numero_chasis IS NOT NULL 
GROUP BY sb.numero_id_fiscal , sb.numero_chasis  
UNION ALL
 SELECT
	sb2.numero_id_fiscal as rut,
	sb2.numero_chasis 
FROM prodDatafile_dev.SAP_BEX4_VENTA_OSORNO sb2
	WHERE 
    YEAR(sb2.dia_natural) = Temporada_Input AND MONTH(sb2.dia_natural) = Periodo_Input AND 
    sb2.marca_derco IS NOT NULL AND sb2.modelo IS NOT NULL AND sb2.numero_chasis IS NOT NULL 
GROUP BY sb2.numero_id_fiscal , sb2.numero_chasis;


## ------------------------------------------------------------------------------------------
## INSERTAMOS LOS DATOS A LA TABLA PARA VINCULAR LAS UNIDADES A LOS CLIENTES
## ------------------------------------------------------------------------------------------
INSERT IGNORE INTO Autogestores_Warehouse.VehiculoCliente (IdVehiculo,IdCliente,Fecha,IndActual)
## VINCULAMOS LAS UNIDADES A LOS CLIENTES QUE CORRESPONDEN
SELECT 
	v.IdVehiculo,
	c.IdClientes,
	v.FechaIngreso,
	0	
FROM Autogestores_Warehouse.temp_lista_clientes_vehiculos_vinculantes s
JOIN Autogestores_Warehouse.Clientes c ON c.Rut = s.rut
JOIN Autogestores_Warehouse.Vehiculos v ON v.NumeroChasis = s.numero_chasis
WHERE NOT EXISTS ( SELECT * FROM Autogestores_Warehouse.VehiculoCliente vc WHERE vc.IdVehiculo = v.IdVehiculo AND vc.IdCliente = c.IdClientes)
GROUP BY c.IdClientes , v.IdVehiculo;

DROP TABLE IF EXISTS Autogestores_Warehouse.temp_lista_clientes_vehiculos_vinculantes;


END
//



