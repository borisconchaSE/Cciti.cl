
DELIMITER //
CREATE EVENT MOVER_DATOS_DESDE_BUC
  ON SCHEDULE
    EVERY 1 DAY
    STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 1 DAY + INTERVAL 12 HOUR)
  DO 
   BEGIN
	   ## CARGAMOS LOS DATOS DE GARAGETRACER
	 	CALL Autogestores_Warehouse.PROCEDURE_CARGAR_DATOS_GARAGETRACER(YEAR( CURRENT_TIME() ));
	 
	 	## CARGAMOS LOS DATOS DE SIGEDYP
		CALL Autogestores_Warehouse.PROCEDURE_INGRESO_SIGEDYP(YEAR( CURRENT_TIME() ));
	
		## CARGAMOS LOS DATOS DE VENTA MESON
		CALL Autogestores_Warehouse.PROCEDURE_GUARDAR_DATOS_VENTA_MESON( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		
		## CARGAMOS LOS DATOS DE CONTROL DE EMISION 
		CALL Autogestores_Warehouse.PROCEDURE_CONTROL_EMISION( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
	
		## CARGAMOS LOS DATOS DE LOS CREDITOS DE AMICAR 
		CALL Autogestores_Warehouse.PROCEDURE_AGREGAR_CREDITOS_AMICAR( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
	
		## CARGAMOS LOS DATOS DE LOS CREDITOS DE AUTOFIN
		CALL Autogestores_Warehouse.PROCEDURE_AGREGAR_CREDITOS_AUTOFIN( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		
		## CARGAMOS LOS DATOS DE LAS VENTAS DE USADOS 
		CALL Autogestores_Warehouse.PROCEDURE_USADOS_CARGA_CLIENTES( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		CALL Autogestores_Warehouse.PROCEDURE_VINCULAR_VEHICULOS_USADOS( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		CALL Autogestores_Warehouse.PROCEDURE_GUARDAR_NEGOCIOS_SERVICIOS_USADOS( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		
		## CARGAMOS LOS DATOS DE LAS VENTAS NUEVOS
		CALL Autogestores_Warehouse.PROCEDURE_CARGAR_CLIENTES_NUEVOS( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		CALL Autogestores_Warehouse.PROCEDURE_VINCULAR_UNIDADES_NUEVOS( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		CALL Autogestores_Warehouse.PROCEDURE_CARGAR_SERVICIOS_NUEVOS( YEAR( CURRENT_TIME() ), month(CURRENT_TIME()));
		
	
	
 	END;
//
 
 