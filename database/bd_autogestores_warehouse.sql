-- MySQL Script generated by MySQL Workbench
-- Fri Jan 19 11:04:13 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema Autogestores_Warehouse
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema Autogestores_Warehouse
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `Autogestores_Warehouse` DEFAULT CHARACTER SET utf8 ;
USE `Autogestores_Warehouse` ;

-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Region`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Region` (
  `IdRegion` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdRegion`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Comuna`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Comuna` (
  `IdComuna` INT NOT NULL AUTO_INCREMENT,
  `IdRegion` INT NULL,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdComuna`),
  INDEX `FK_Rregion_Comuna_idx` (`IdRegion` ASC) VISIBLE,
  CONSTRAINT `FK_Rregion_Comuna`
    FOREIGN KEY (`IdRegion`)
    REFERENCES `Autogestores_Warehouse`.`Region` (`IdRegion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Ciudad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Ciudad` (
  `IdCiudad` INT NOT NULL AUTO_INCREMENT,
  `IdComuna` INT NULL,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdCiudad`),
  INDEX `FK_CIUDAD_COMUNA_idx` (`IdComuna` ASC) VISIBLE,
  CONSTRAINT `FK_CIUDAD_COMUNA`
    FOREIGN KEY (`IdComuna`)
    REFERENCES `Autogestores_Warehouse`.`Comuna` (`IdComuna`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Clientes` (
  `IdClientes` INT NOT NULL AUTO_INCREMENT,
  `NombreCliente` VARCHAR(255) NULL,
  `CorreoCliente` VARCHAR(255) NULL,
  `TelefonoPrimario` INT NULL,
  `TelefonoSecundario` INT NULL,
  `TelefonoOpcional` INT NULL,
  `IdCiudad` INT NULL DEFAULT 1,
  `Rut` VARCHAR(45) NULL,
  PRIMARY KEY (`IdClientes`),
  INDEX `FK_Cliente_Ciudad_idx` (`IdCiudad` ASC) VISIBLE,
  CONSTRAINT `FK_Cliente_Ciudad`
    FOREIGN KEY (`IdCiudad`)
    REFERENCES `Autogestores_Warehouse`.`Ciudad` (`IdCiudad`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Sucursal`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Sucursal` (
  `IdSucursal` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  `IdCiudad` INT NULL,
  PRIMARY KEY (`IdSucursal`),
  INDEX `FK_SUCURSAL_CIUDAD_idx` (`IdCiudad` ASC) VISIBLE,
  CONSTRAINT `FK_SUCURSAL_CIUDAD`
    FOREIGN KEY (`IdCiudad`)
    REFERENCES `Autogestores_Warehouse`.`Ciudad` (`IdCiudad`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Centro`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Centro` (
  `IdCentro` INT NOT NULL AUTO_INCREMENT,
  `Codigo` VARCHAR(255) NOT NULL,
  `Descripcion` VARCHAR(255) NOT NULL,
  `IdSucursal` INT NOT NULL,
  PRIMARY KEY (`IdCentro`),
  INDEX `FK_CENTRO_CIUDAD_idx` (`IdSucursal` ASC) VISIBLE,
  CONSTRAINT `FK_CENTRO_CIUDAD`
    FOREIGN KEY (`IdSucursal`)
    REFERENCES `Autogestores_Warehouse`.`Sucursal` (`IdSucursal`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`TipoServicio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`TipoServicio` (
  `IdTipoServicio` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdTipoServicio`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Plataforma`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Plataforma` (
  `IdPlataforma` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdPlataforma`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Servicio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Servicio` (
  `IdServicio` INT NOT NULL AUTO_INCREMENT,
  `IdCliente` INT NULL,
  `IdTipoServicio` INT NULL,
  `IdCentro` INT NULL,
  `FechaServicio` DATETIME NULL,
  `IdPlataformaOrigen` INT NULL,
  `IdUsuarioPropietario` INT NULL DEFAULT 1,
  PRIMARY KEY (`IdServicio`),
  INDEX `FK_TipoServicio_idx` (`IdTipoServicio` ASC) VISIBLE,
  INDEX `FK_Servicio_Cliente_idx` (`IdCliente` ASC) VISIBLE,
  INDEX `FK_Servicio_Centro_idx` (`IdCentro` ASC) VISIBLE,
  INDEX `FK_Servicio_Plataforma_idx` (`IdPlataformaOrigen` ASC) VISIBLE,
  CONSTRAINT `FK_TipoServicio`
    FOREIGN KEY (`IdTipoServicio`)
    REFERENCES `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_Servicio_Cliente`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_Servicio_Centro`
    FOREIGN KEY (`IdCentro`)
    REFERENCES `Autogestores_Warehouse`.`Centro` (`IdCentro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_Servicio_Plataforma`
    FOREIGN KEY (`IdPlataformaOrigen`)
    REFERENCES `Autogestores_Warehouse`.`Plataforma` (`IdPlataforma`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Vehiculos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Vehiculos` (
  `IdVehiculo` INT NOT NULL AUTO_INCREMENT,
  `Marca` VARCHAR(255) NULL,
  `Modelo` VARCHAR(255) NULL,
  `Patente` VARCHAR(255) NULL,
  `NumeroChasis` VARCHAR(60) NULL,
  `FechaIngreso` DATETIME NULL,
  `Kilometraje` VARCHAR(45) NULL,
  PRIMARY KEY (`IdVehiculo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioVenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioVenta` (
  `IdServicioVenta` INT NOT NULL AUTO_INCREMENT,
  `IdServicio` INT NOT NULL,
  `ClasePedido` VARCHAR(255) NULL,
  `Marca` VARCHAR(255) NULL,
  `Modelo` VARCHAR(255) NULL,
  `Patente` VARCHAR(45) NULL,
  `Chasis` VARCHAR(255) NULL,
  `Color` VARCHAR(45) NULL,
  `NumeroPedido` VARCHAR(45) NULL,
  `FechaPedido` DATETIME NULL,
  `NumeroFactura` VARCHAR(45) NULL,
  `FechaFactura` DATETIME NULL,
  `TipoPedido` VARCHAR(255) NULL,
  `AñoModelo` INT NULL,
  `IndCompraPara` TINYINT NULL,
  `DPS` INT NULL,
  `DPE` INT NULL,
  `PrecioLista` INT NULL,
  `PrecioCompra` INT NULL,
  `Descuentos` INT NULL,
  `Accesorios` INT NULL,
  `PrecioVenta` INT NULL,
  `CargosVehiculo` INT NULL,
  `GastosProvicionados` INT NULL,
  `Margen` INT NULL,
  `NumeroStock` VARCHAR(45) NULL,
  `CodigoVehiculo` VARCHAR(45) NULL,
  `FechaRecepcion` DATETIME NULL,
  `AutorizadoPor` VARCHAR(125) NULL,
  `InstitucionCredito` VARCHAR(255) NULL,
  `ValorCredito` INT NULL,
  `MarcaRetoma` VARCHAR(45) NULL,
  `ModeloRetoma` VARCHAR(255) NULL,
  `AñoRetoma` INT NULL,
  `ValorRetoma` INT NULL,
  `CodigoRetoma` VARCHAR(45) NULL,
  `PatenteRetoma` VARCHAR(45) NULL,
  `PagoContado` INT NULL,
  `PagoDocumentado` INT NULL,
  `PagoCreditoSimple` INT NULL,
  `PagoLeasing` INT NULL,
  `GrupoModelo` VARCHAR(255) NULL,
  `TipoUnidad` VARCHAR(255) NULL,
  `Sociedad` VARCHAR(45) NULL,
  `Vendedor` VARCHAR(255) NULL,
  `NombreCompraPara` VARCHAR(255) NULL,
  `RutCompraPara` VARCHAR(25) NULL,
  `IdVehiculo` INT NULL,
  PRIMARY KEY (`IdServicioVenta`),
  INDEX `FK_SERVICIOVENTA_SERVICIO_idx` (`IdServicio` ASC) VISIBLE,
  INDEX `FK_SV_VEHICULO_idx` (`IdVehiculo` ASC) VISIBLE,
  CONSTRAINT `FK_SERVICIOVENTA_SERVICIO`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SV_VEHICULO`
    FOREIGN KEY (`IdVehiculo`)
    REFERENCES `Autogestores_Warehouse`.`Vehiculos` (`IdVehiculo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`InstitucionCredito`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`InstitucionCredito` (
  `IdInstitucionCredito` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdInstitucionCredito`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`EstadoCredito`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`EstadoCredito` (
  `IdEstadoCredito` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdEstadoCredito`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioCredito`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioCredito` (
  `IdServicioCredito` INT NOT NULL AUTO_INCREMENT,
  `IdServicio` INT NULL,
  `IdInstitucionCredito` INT NULL,
  `IdPlataforma` INT NULL,
  `Marca` VARCHAR(45) NULL,
  `Modelo` VARCHAR(255) NULL,
  `AñoModelo` INT NULL,
  `NombreProducto` VARCHAR(255) NULL,
  `EstadoAuto` VARCHAR(45) NULL,
  `Precio` INT NULL,
  `Pie` INT NULL,
  `MontoPagare` INT NULL,
  `Plazo` INT NULL,
  `SaldoPrecio` INT NULL,
  `TasaCurse` INT NULL,
  `ValorCuota` INT NULL,
  `Sueldo` INT NULL,
  `TipoVehiculo` VARCHAR(45) NULL,
  `Canal` VARCHAR(45) NULL,
  `IdEstadoCredito` INT NULL,
  `Vendedor` VARCHAR(255) NULL,
  PRIMARY KEY (`IdServicioCredito`),
  INDEX `FK_ServicioC_servicio_idx` (`IdServicio` ASC) VISIBLE,
  INDEX `FK_SC_CreditoInsti_idx` (`IdInstitucionCredito` ASC) VISIBLE,
  INDEX `FK_SC_Plataforma_idx` (`IdPlataforma` ASC) VISIBLE,
  INDEX `FK_SC_EstadoCredito_idx` (`IdEstadoCredito` ASC) VISIBLE,
  CONSTRAINT `FK_ServicioC_servicio`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SC_CreditoInsti`
    FOREIGN KEY (`IdInstitucionCredito`)
    REFERENCES `Autogestores_Warehouse`.`InstitucionCredito` (`IdInstitucionCredito`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SC_Plataforma`
    FOREIGN KEY (`IdPlataforma`)
    REFERENCES `Autogestores_Warehouse`.`Plataforma` (`IdPlataforma`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SC_EstadoCredito`
    FOREIGN KEY (`IdEstadoCredito`)
    REFERENCES `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`TipoLista`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`TipoLista` (
  `IdTipoLista` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdTipoLista`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ListaClientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ListaClientes` (
  `IdListaCliente` INT NOT NULL AUTO_INCREMENT,
  `IdPropietario` INT NULL,
  `Descripcion` TEXT NULL,
  `Activa` TINYINT NULL,
  `Editable` TINYINT NULL,
  `FechaCreacion` DATETIME NULL,
  `IdTipoLista` INT NULL,
  PRIMARY KEY (`IdListaCliente`),
  INDEX `FK_LISTA_CLIENTES_TIPO_LISTA_idx` (`IdTipoLista` ASC) VISIBLE,
  CONSTRAINT `FK_LISTA_CLIENTES_TIPO_LISTA`
    FOREIGN KEY (`IdTipoLista`)
    REFERENCES `Autogestores_Warehouse`.`TipoLista` (`IdTipoLista`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`EstadoGestion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`EstadoGestion` (
  `IdEstadoGestion` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdEstadoGestion`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`DetalleListaCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`DetalleListaCliente` (
  `IdDetalleListaCliente` INT NOT NULL AUTO_INCREMENT,
  `IdListaCliente` INT NULL,
  `IdUsuario` INT NULL,
  `IdCliente` INT NULL,
  `FechaIngreso` DATETIME NULL,
  `FechaActualizacion` DATETIME NULL,
  `IdEstadoGestion` INT NULL,
  PRIMARY KEY (`IdDetalleListaCliente`),
  INDEX `FK_Detalle_Lista_idx` (`IdListaCliente` ASC) VISIBLE,
  INDEX `FK_Detalle_Cliente_idx` (`IdCliente` ASC) VISIBLE,
  INDEX `FK_DETALLE_CLIENTE_ETADO_GESTION_idx` (`IdEstadoGestion` ASC) VISIBLE,
  CONSTRAINT `FK_Detalle_Lista`
    FOREIGN KEY (`IdListaCliente`)
    REFERENCES `Autogestores_Warehouse`.`ListaClientes` (`IdListaCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_Detalle_Cliente`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_DETALLE_CLIENTE_ETADO_GESTION`
    FOREIGN KEY (`IdEstadoGestion`)
    REFERENCES `Autogestores_Warehouse`.`EstadoGestion` (`IdEstadoGestion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`TipoObservacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`TipoObservacion` (
  `IdTipoObservacion` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdTipoObservacion`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ObservacionCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ObservacionCliente` (
  `IdComentariosCliente` INT NOT NULL AUTO_INCREMENT,
  `IdCliente` INT NULL,
  `IdUsuario` INT NULL,
  `IdTIpoObservacion` INT NULL,
  `Observacion` TEXT NULL,
  PRIMARY KEY (`IdComentariosCliente`),
  INDEX `FK_OBSERVACION_CLIENTE_idx` (`IdCliente` ASC) VISIBLE,
  INDEX `FK_OBSERVACION_TIPO_OBSERVACION_idx` (`IdTIpoObservacion` ASC) VISIBLE,
  CONSTRAINT `FK_OBSERVACION_CLIENTE`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_OBSERVACION_TIPO_OBSERVACION`
    FOREIGN KEY (`IdTIpoObservacion`)
    REFERENCES `Autogestores_Warehouse`.`TipoObservacion` (`IdTipoObservacion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`Tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`Tag` (
  `IdTag` INT NOT NULL AUTO_INCREMENT,
  `Descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`IdTag`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`TagCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`TagCliente` (
  `IdTagCliente` INT NOT NULL AUTO_INCREMENT,
  `IdTag` INT NULL,
  `IdCliente` INT NULL,
  PRIMARY KEY (`IdTagCliente`),
  INDEX `FK_TAG_TAGCLIENTE_idx` (`IdTag` ASC) VISIBLE,
  INDEX `FK_TAG_CLIENTE_idx` (`IdCliente` ASC) VISIBLE,
  CONSTRAINT `FK_TAG_TAGCLIENTE`
    FOREIGN KEY (`IdTag`)
    REFERENCES `Autogestores_Warehouse`.`Tag` (`IdTag`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_TAG_CLIENTE`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`VehiculoCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`VehiculoCliente` (
  `IdVehiculoCliente` INT NOT NULL AUTO_INCREMENT,
  `IdVehiculo` INT NULL,
  `IdCliente` INT NULL,
  `Fecha` DATETIME NULL,
  `IndActual` TINYINT NULL,
  PRIMARY KEY (`IdVehiculoCliente`),
  INDEX `FK_Vehiculo_Cliente_idx` (`IdCliente` ASC) VISIBLE,
  INDEX `FK_VC_Vehiculo_idx` (`IdVehiculo` ASC) VISIBLE,
  CONSTRAINT `FK_Vehiculo_Cliente`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_VC_Vehiculo`
    FOREIGN KEY (`IdVehiculo`)
    REFERENCES `Autogestores_Warehouse`.`Vehiculos` (`IdVehiculo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`HistorialKilometraje`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`HistorialKilometraje` (
  `IdHistorialKilometraje` INT NOT NULL AUTO_INCREMENT,
  `IdVehiculo` INT NULL,
  `Kilometraje` INT NULL,
  `Fecha` DATETIME NULL,
  PRIMARY KEY (`IdHistorialKilometraje`),
  INDEX `FK_HK_Vehiculo_idx` (`IdVehiculo` ASC) VISIBLE,
  CONSTRAINT `FK_HK_Vehiculo`
    FOREIGN KEY (`IdVehiculo`)
    REFERENCES `Autogestores_Warehouse`.`Vehiculos` (`IdVehiculo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`EvaluacionCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`EvaluacionCliente` (
  `IdEvaluacionCliente` INT NOT NULL AUTO_INCREMENT,
  `IdCliente` INT NOT NULL,
  `IdLista` INT NULL,
  `Puntuacion` INT NULL,
  `Fecha` DATETIME NULL,
  `IdUsuario` INT NULL,
  PRIMARY KEY (`IdEvaluacionCliente`),
  INDEX `FK_EVAL_CLIENTE_idx` (`IdCliente` ASC) VISIBLE,
  INDEX `FK_EVAL_LISTA_idx` (`IdLista` ASC) VISIBLE,
  CONSTRAINT `FK_EVAL_CLIENTE`
    FOREIGN KEY (`IdCliente`)
    REFERENCES `Autogestores_Warehouse`.`Clientes` (`IdClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_EVAL_LISTA`
    FOREIGN KEY (`IdLista`)
    REFERENCES `Autogestores_Warehouse`.`ListaClientes` (`IdListaCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioPostVentaBex`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioPostVentaBex` (
  `IdServicioTaller` INT NOT NULL AUTO_INCREMENT,
  `IdPedido` INT NULL,
  `MotivoPedido` VARCHAR(255) NULL,
  `NombreConsultor` VARCHAR(255) NULL,
  `IdCentro` INT NULL,
  `NombreCliente` VARCHAR(255) NULL,
  `NumeroVehiculo` VARCHAR(125) NULL,
  `OTPromedio` INT NULL,
  `Margen` INT NULL,
  `CantidadOT` VARCHAR(45) NULL,
  `VentaNeta` INT NULL,
  `Descuento` INT NULL,
  `Repuestos` INT NULL,
  `ManoDeObra` INT NULL,
  `PrecioLista` INT NULL,
  `CostoVenta` INT NULL,
  `Marca` VARCHAR(125) NULL,
  `Modelo` VARCHAR(125) NULL,
  `IdServicio` INT NULL,
  `Fecha` DATETIME NULL,
  `IdVehiculo` INT NULL,
  PRIMARY KEY (`IdServicioTaller`),
  INDEX `FK_STALLER_CENTRO_idx` (`IdCentro` ASC) VISIBLE,
  INDEX `FK_STALLER_SERVICIO_idx` (`IdServicio` ASC) VISIBLE,
  INDEX `FK_STALLER_VEHICULO_idx` (`IdVehiculo` ASC) VISIBLE,
  CONSTRAINT `FK_STALLER_CENTRO`
    FOREIGN KEY (`IdCentro`)
    REFERENCES `Autogestores_Warehouse`.`Centro` (`IdCentro`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_STALLER_SERVICIO`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_STALLER_VEHICULO`
    FOREIGN KEY (`IdVehiculo`)
    REFERENCES `Autogestores_Warehouse`.`Vehiculos` (`IdVehiculo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioGT`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioGT` (
  `IdServicioGT` INT NOT NULL AUTO_INCREMENT,
  `IdServicio` INT NOT NULL,
  `Marca` VARCHAR(45) NULL,
  `Modelo` VARCHAR(255) NULL,
  `Patente` VARCHAR(25) NULL,
  `CodigoMotor` VARCHAR(45) NULL,
  `NumeroChasis` VARCHAR(45) NULL,
  `Kilometraje` INT NULL,
  `AnioModelo` INT NULL,
  `ColorVehiculo` VARCHAR(45) NULL,
  `NombreCliente` VARCHAR(45) NULL,
  `RutCliente` VARCHAR(45) NULL,
  `MotivoIngreso` VARCHAR(45) NULL,
  `DetalleIngreso` VARCHAR(255) NULL,
  `DetalleCondicion` VARCHAR(255) NULL,
  `IdVehiculo` INT NOT NULL,
  PRIMARY KEY (`IdServicioGT`),
  INDEX `FK_SG_S_idx` (`IdServicio` ASC) VISIBLE,
  INDEX `FK_SG_V_idx` (`IdVehiculo` ASC) VISIBLE,
  CONSTRAINT `FK_SG_S`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SG_V`
    FOREIGN KEY (`IdVehiculo`)
    REFERENCES `Autogestores_Warehouse`.`Vehiculos` (`IdVehiculo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioVentaMeson`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioVentaMeson` (
  `IdServicioVentaMeson` INT NOT NULL AUTO_INCREMENT,
  `Codigo` INT NULL,
  `IdServicio` INT NOT NULL,
  `Vendedor` VARCHAR(255) NULL,
  `NumeroDocumento` VARCHAR(125) NULL,
  `Fecha` DATETIME NULL,
  `FechaReferencia` DATETIME NULL,
  `Cantidad` INT NULL,
  `TotalVenta` INT NULL,
  `Descuentos` INT NULL,
  `TotalCobrado` INT NULL,
  `Costo` INT NULL,
  `Margen` INT NULL,
  `Porcentaje` FLOAT NULL,
  `Familia` VARCHAR(125) NULL,
  `Clasificacion` VARCHAR(125) NULL,
  `Marca` VARCHAR(255) NULL,
  `Categoria` VARCHAR(255) NULL,
  `NumeroCotizacion` INT NULL,
  `CV` INT NULL,
  `NumeroParte` VARCHAR(125) NULL,
  PRIMARY KEY (`IdServicioVentaMeson`),
  INDEX `FK_SVM_S_idx` (`IdServicio` ASC) VISIBLE,
  CONSTRAINT `FK_SVM_S`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Autogestores_Warehouse`.`ServicioControlEmision`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Autogestores_Warehouse`.`ServicioControlEmision` (
  `IdServicioControlEmision` INT NOT NULL AUTO_INCREMENT,
  `IdServicio` INT NOT NULL,
  `Numero` INT NULL,
  `EnviaSII` TINYINT NULL,
  `RecSII` TINYINT NULL,
  `AcuRec` TINYINT NULL,
  `AceptaReclamo` VARCHAR(45) NULL,
  `EmitidaEn` VARCHAR(255) NULL,
  `Departamento` VARCHAR(255) NULL,
  `Cliente` VARCHAR(45) NULL,
  `OrdenDeCommpra` VARCHAR(45) NULL,
  `Neto` INT NULL,
  `Exento` INT NULL,
  `IVA` INT NULL,
  `IvaFueraDePlazo` INT NULL,
  `OtrosImpuestos` INT NULL,
  `Total` INT NULL,
  `NcRescil` INT NULL,
  `NFCPlazo` INT NULL,
  `NCAdmin` INT NULL,
  `Caja` VARCHAR(45) NULL,
  `FechaCaja` DATETIME NULL,
  `TipoEmision` INT NULL,
  `Referencia` VARCHAR(45) NULL,
  `Usuario` VARCHAR(125) NULL,
  `FechaDigitacion` DATETIME NULL,
  `Origen` VARCHAR(45) NULL,
  PRIMARY KEY (`IdServicioControlEmision`),
  INDEX `FK_SCE_S_idx` (`IdServicio` ASC) VISIBLE,
  CONSTRAINT `FK_SCE_S`
    FOREIGN KEY (`IdServicio`)
    REFERENCES `Autogestores_Warehouse`.`Servicio` (`IdServicio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `Autogestores_Warehouse`.`Region`
-- -----------------------------------------------------
START TRANSACTION;
USE `Autogestores_Warehouse`;
INSERT INTO `Autogestores_Warehouse`.`Region` (`IdRegion`, `Descripcion`) VALUES (1, 'SIN REGIÓN');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Autogestores_Warehouse`.`Comuna`
-- -----------------------------------------------------
START TRANSACTION;
USE `Autogestores_Warehouse`;
INSERT INTO `Autogestores_Warehouse`.`Comuna` (`IdComuna`, `IdRegion`, `Descripcion`) VALUES (1, 1, 'SIN COMUNA');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Autogestores_Warehouse`.`Ciudad`
-- -----------------------------------------------------
START TRANSACTION;
USE `Autogestores_Warehouse`;
INSERT INTO `Autogestores_Warehouse`.`Ciudad` (`IdCiudad`, `IdComuna`, `Descripcion`) VALUES (1, 1, 'SIN CIUDAD');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Autogestores_Warehouse`.`TipoServicio`
-- -----------------------------------------------------
START TRANSACTION;
USE `Autogestores_Warehouse`;
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (1, 'Venta Nuevos');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (2, 'Venta Usados');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (3, 'Postventa');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (4, 'Lead Nuevos');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (5, 'Lead Usados');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (6, 'Creditos Nuevos');
INSERT INTO `Autogestores_Warehouse`.`TipoServicio` (`IdTipoServicio`, `Descripcion`) VALUES (7, 'Creditos Usados');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Autogestores_Warehouse`.`EstadoCredito`
-- -----------------------------------------------------
START TRANSACTION;
USE `Autogestores_Warehouse`;
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (1, 'Aprobada');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (2, 'Rechazado');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (3, 'Otorgada');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (4, 'Solicitud');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (5, 'Cotización');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (6, 'Devuelto');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (7, 'Cursado');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (8, 'Pre-Curse');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (9, 'Condicionado');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (10, 'RevisionFirma');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (11, 'Anulado');
INSERT INTO `Autogestores_Warehouse`.`EstadoCredito` (`IdEstadoCredito`, `Descripcion`) VALUES (12, 'Evaluación');

COMMIT;

