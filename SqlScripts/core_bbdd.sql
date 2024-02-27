
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `Cliente`
--

DROP TABLE IF EXISTS `Cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cliente` (
  `IdCliente` int NOT NULL AUTO_INCREMENT,
  `IdTipoCliente` int NOT NULL,
  `RazonSocial` varchar(256) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Rut` varchar(15) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Direccion` varchar(512) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Eliminado` tinyint(1) DEFAULT NULL,
  `UriLogo` varchar(512) COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT 'logo.png',
  `IdTipoProducto` int NOT NULL DEFAULT '1',
  `IndTurnoDiscontinuo` tinyint DEFAULT '0',
  `UriLogoAlternativo` varchar(512) COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT '',
  `IdEstilo` int DEFAULT NULL,
  PRIMARY KEY (`IdCliente`),
  KEY `fk_Tipo_Cliente_1_idx` (`IdTipoCliente`),
  KEY `IdTipoProducto` (`IdTipoProducto`),
  CONSTRAINT `Cliente_ibfk_1` FOREIGN KEY (`IdTipoProducto`) REFERENCES `TipoProducto` (`IdTipoProducto`),
  CONSTRAINT `fk_Tipo_Cliente_1` FOREIGN KEY (`IdTipoCliente`) REFERENCES `TipoCliente` (`IdTipoCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cliente`
--

LOCK TABLES `Cliente` WRITE;
/*!40000 ALTER TABLE `Cliente` DISABLE KEYS */;
INSERT INTO `Cliente` VALUES (1,1,'Sergio Escobar','1-9','Paicavi',0,'logo_sergio_escobar_app.png',1,0,'',NULL);
/*!40000 ALTER TABLE `Cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contacto`
--

DROP TABLE IF EXISTS `Contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contacto` (
  `IdContacto` int NOT NULL AUTO_INCREMENT,
  `IdCliente` int DEFAULT NULL,
  `Nombre` varchar(512) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Cargo` varchar(512) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Email` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Telefono` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Eliminado` tinyint(1) DEFAULT NULL,
  `Avatar` varchar(45) COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`IdContacto`),
  KEY `fk_Cliente_1_idx` (`IdCliente`),
  CONSTRAINT `Contacto_Cliente_FK` FOREIGN KEY (`IdCliente`) REFERENCES `Cliente` (`IdCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contacto`
--

LOCK TABLES `Contacto` WRITE;
/*!40000 ALTER TABLE `Contacto` DISABLE KEYS */;
INSERT INTO `Contacto` VALUES (1,1,'Juan E. Vivallos E.','Director de Proyectos','jvivallos@resolved.cl','',0,'jvivallos.png'),(2,1,'Diego Mucientes','Ingeniero de Procesos','diegomucientes@sergioescobar.cl','',0,'dmucientes.png'),(3,1,'Andrés Farías','Vendedor','andresfarias@sergioescobar.cl','',0,''),(4,1,'Juan Pablo Rodríguez','Ingeniero de Software','juanpablorodriguez@sergioescobar.cl','',0,''),(5,1,'Natalia Miranda','Jefe de Venta','nataliamiranda@sergioescobar.cl','',0,''),(6,1,'Gerson Oyarce','Gerente','gersonoyarce@sergioescobar.cl','',0,''),(7,1,'Kriss Martinez','Jefe de Pre Entrega','krissmartinez@sergioescobar.cl','',0,''),(8,1,'Benjamin Arellano','Administración','benjaminarellano@sergioescobar.cl','',0,'');
/*!40000 ALTER TABLE `Contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Empresa`
--

DROP TABLE IF EXISTS `Empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Empresa` (
  `IdEmpresa` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(256) NOT NULL DEFAULT '',
  `IdCliente` int NOT NULL,
  `Logo` varchar(256) DEFAULT NULL,
  `Alias` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdEmpresa`),
  KEY `fk_emp_cli` (`IdCliente`),
  CONSTRAINT `Empresa_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `Cliente` (`IdCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Empresa`
--

LOCK TABLES `Empresa` WRITE;
/*!40000 ALTER TABLE `Empresa` DISABLE KEYS */;
INSERT INTO `Empresa` VALUES (1,'Sergio Escobar',1,NULL,'sergioescobar');
/*!40000 ALTER TABLE `Empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Estilo`
--

DROP TABLE IF EXISTS `Estilo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Estilo` (
  `IdEstilo` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  `IndHost` tinyint(1) DEFAULT '0',
  `IdTipoLogin` int NOT NULL DEFAULT '1',
  `LogoAlternativo` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdEstilo`),
  KEY `fk_est_tiplog` (`IdTipoLogin`),
  CONSTRAINT `fk_est_tiplog` FOREIGN KEY (`IdTipoLogin`) REFERENCES `TipoLogin` (`IdTipoLogin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Estilo`
--

LOCK TABLES `Estilo` WRITE;
/*!40000 ALTER TABLE `Estilo` DISABLE KEYS */;
INSERT INTO `Estilo` VALUES (1,'chexo.resolved.cl',1,3,'');
/*!40000 ALTER TABLE `Estilo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Perfil`
--

DROP TABLE IF EXISTS `Perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Perfil` (
  `IdPerfil` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(256) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Eliminado` tinyint(1) DEFAULT NULL,
  `LandingPage` varchar(256) COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT '/mantencion/activas',
  `IndSeleccionable` int NOT NULL DEFAULT '0',
  `IdTipoProducto` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`IdPerfil`),
  KEY `Perfil_Eliminado_IDX` (`Eliminado`) USING BTREE,
  KEY `Perfil_FK` (`IdTipoProducto`),
  CONSTRAINT `Perfil_FK` FOREIGN KEY (`IdTipoProducto`) REFERENCES `TipoProducto` (`IdTipoProducto`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Perfil`
--

LOCK TABLES `Perfil` WRITE;
/*!40000 ALTER TABLE `Perfil` DISABLE KEYS */;
INSERT INTO `Perfil` VALUES (0,'Resolved',0,'/home',0,1),(1,'Gerencia Automatización',0,'/stock/consultastock/consultar',1,1),(2,'Vendedor',0,'/home',0,1),(3,'Jefe de Venta',0,'/home',0,1),(4,'Gerente',0,'/home',0,1),(5,'Jefe de Pre Entrega',0,'/home',0,1),(6,'Administración',0,'/home',0,1);
/*!40000 ALTER TABLE `Perfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Rol`
--

DROP TABLE IF EXISTS `Rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rol` (
  `IdRol` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(256) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Eliminado` tinyint(1) DEFAULT NULL,
  `Codigo` varchar(110) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`IdRol`),
  KEY `Rol_Eliminado_IDX` (`Eliminado`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Rol`
--

LOCK TABLES `Rol` WRITE;
/*!40000 ALTER TABLE `Rol` DISABLE KEYS */;
INSERT INTO `Rol` VALUES (1,'Consultar la pantalla de stock',0,'R_CONSULTA_STOCK'),(2,'Ver Solicitud Compra',0,'R_VER_SOLICITUD'),(3,'Ingresar Solicitud Compra',0,'R_INGRESAR_SOLICITUD'),(4,'Eliminar Solicitud Compra',0,'R_ELIMINAR_SOLICITUD'),(5,'Aprobar o Rechazar Solicitud Compra',0,'R_APROBAR_RECHAZAR_SOLICITUD'),(6,'Editar Solicitud Compra',0,'R_EDITAR_SOLICITUD'),(7,'Consultar Solicitud de PreEntrega',0,'R_CONSULTA_SOLPREENTREGA'),(8,'Ingresar Solicitud de PreEntrega',0,'R_INGRESAR_SOLPREENTREGA'),(9,'Aprobar o Rechazar una solicitud de Pre Entrega',0,'R_APROBAR_RECHAZAR_SOLPREENTREGA'),(10,'Eliminar Solicitud de PreEntrega',0,'R_ELIMINAR_SOLPREENTREGA'),(11,'Editar Solicitud de PreEntrega',0,'R_EDITAR_SOLPREENTREGA'),(12,'Gestionar Checklist de Preentrega aprobasdas',0,'R_GESTIONAR_PREENTREGA'),(13,'Aprobar PreEntrega',0,'R_APROBAR_PREENTREGA');
/*!40000 ALTER TABLE `Rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RolPorPerfil`
--

DROP TABLE IF EXISTS `RolPorPerfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `RolPorPerfil` (
  `IdRolPorPerfil` int NOT NULL AUTO_INCREMENT,
  `IdRol` int DEFAULT NULL,
  `IdPerfil` int DEFAULT NULL,
  PRIMARY KEY (`IdRolPorPerfil`),
  KEY `fk_ROL_1_idx` (`IdRol`) USING BTREE,
  KEY `RolPorPerfil_Perfil_FK` (`IdPerfil`),
  CONSTRAINT `RolPorPerfil_FK` FOREIGN KEY (`IdRol`) REFERENCES `Rol` (`IdRol`),
  CONSTRAINT `RolPorPerfil_Perfil_FK` FOREIGN KEY (`IdPerfil`) REFERENCES `Perfil` (`IdPerfil`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RolPorPerfil`
--

LOCK TABLES `RolPorPerfil` WRITE;
/*!40000 ALTER TABLE `RolPorPerfil` DISABLE KEYS */;
INSERT INTO `RolPorPerfil` VALUES (1,1,1),(2,1,0),(3,2,0),(4,3,0),(5,4,0),(6,5,0),(7,6,0),(8,7,0),(9,8,0),(10,9,0),(11,10,0),(12,11,0),(13,12,0),(14,1,2),(15,2,2),(16,3,2),(17,4,2),(18,6,2),(19,7,2),(20,1,3),(21,2,3),(22,3,3),(23,4,3),(24,6,3),(25,7,3),(26,8,3),(27,10,3),(28,11,3),(29,13,3),(30,1,4),(31,2,4),(32,5,4),(33,7,4),(34,7,5),(35,9,5),(36,12,5),(37,13,0),(38,1,6),(39,2,6),(40,3,6),(41,4,6),(42,5,6),(43,6,6),(44,7,6),(45,8,6),(46,9,6),(47,10,6),(48,11,6),(49,12,6),(50,13,6);
/*!40000 ALTER TABLE `RolPorPerfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoClave`
--

DROP TABLE IF EXISTS `TipoClave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoClave` (
  `IdTipoClave` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`IdTipoClave`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoClave`
--

LOCK TABLES `TipoClave` WRITE;
/*!40000 ALTER TABLE `TipoClave` DISABLE KEYS */;
INSERT INTO `TipoClave` VALUES (1,'Inicial'),(2,'Definitiva');
/*!40000 ALTER TABLE `TipoClave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoCliente`
--

DROP TABLE IF EXISTS `TipoCliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoCliente` (
  `IdTipoCliente` int NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`IdTipoCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoCliente`
--

LOCK TABLES `TipoCliente` WRITE;
/*!40000 ALTER TABLE `TipoCliente` DISABLE KEYS */;
INSERT INTO `TipoCliente` VALUES (1,'Concesionario Automotriz');
/*!40000 ALTER TABLE `TipoCliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoEstilo`
--

DROP TABLE IF EXISTS `TipoEstilo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoEstilo` (
  `IdTipoEstilo` varchar(100) NOT NULL,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`IdTipoEstilo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoEstilo`
--

LOCK TABLES `TipoEstilo` WRITE;
/*!40000 ALTER TABLE `TipoEstilo` DISABLE KEYS */;
INSERT INTO `TipoEstilo` VALUES ('BTN_DANGER','Color de peligro del los botones, labels y tarjetas'),('BTN_INFO','Color de información de los botones, labels y tarjetas'),('BTN_PRIMARY','Color primario de los botones, labels y tarjetas'),('BTN_SECONDARY','Color secundario de los botones, labels y tarjetas'),('BTN_SUCCESS','Color de exito en los botones, labels y tarjetas'),('BTN_TEXT_DANGER','color del texto de los botones de peligro o errores'),('BTN_TEXT_INFO','Color del texto de los botones de información'),('BTN_TEXT_PRIMARY','Color del Texto de los botones primarios'),('BTN_TEXT_SECONDARY','Color del texto de los botones secundarios'),('BTN_TEXT_SUCCESS','Color del texto de los botones success'),('BTN_TEXT_WARNING','Color del texto de los botones de alerta'),('BTN_WARNING','Color de alerta de los botones,labels y tarjetas'),('MENU_TEXT_TITLE','Color de los titulos de los submenus  dropdown'),('NAVBAR_MENU_ACTIVE_TEXT','Color del texto de la detención Activa'),('NAVBAR_MENU_ARCHIVADA_TEXT','Color del texto de la detención archivada activa'),('NAVBAR_MENU_BACKGROUND','Color de fondo en la sección donde van los menus'),('NAVBAR_MENU_TEXT','color de los textos de los menus'),('PRIMARY_COLOR','Color corporativo del Navbar y zonas resaltadas de la aplicacion');
/*!40000 ALTER TABLE `TipoEstilo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoIdioma`
--

DROP TABLE IF EXISTS `TipoIdioma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoIdioma` (
  `IdTipoIdioma` int NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(2) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'es',
  `Descripcion` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Español',
  PRIMARY KEY (`IdTipoIdioma`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoIdioma`
--

LOCK TABLES `TipoIdioma` WRITE;
/*!40000 ALTER TABLE `TipoIdioma` DISABLE KEYS */;
INSERT INTO `TipoIdioma` VALUES (1,'es','Español'),(2,'en','English');
/*!40000 ALTER TABLE `TipoIdioma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoLogin`
--

DROP TABLE IF EXISTS `TipoLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoLogin` (
  `IdTipoLogin` int NOT NULL,
  `Descripcion` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdTipoLogin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoLogin`
--

LOCK TABLES `TipoLogin` WRITE;
/*!40000 ALTER TABLE `TipoLogin` DISABLE KEYS */;
INSERT INTO `TipoLogin` VALUES (1,'Slideshow'),(2,'LogoAlternativo'),(3,'Vacio'),(4,'Bajada');
/*!40000 ALTER TABLE `TipoLogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TipoProducto`
--

DROP TABLE IF EXISTS `TipoProducto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TipoProducto` (
  `IdTipoProducto` int NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(6) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'MINING',
  `Descripcion` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DETENCIONES MINERIA',
  PRIMARY KEY (`IdTipoProducto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TipoProducto`
--

LOCK TABLES `TipoProducto` WRITE;
/*!40000 ALTER TABLE `TipoProducto` DISABLE KEYS */;
INSERT INTO `TipoProducto` VALUES (1,'MINING','DETENCIONES MINERIA'),(2,'SUSTEN','PROYECTOS SUSTENTABILIDAD');
/*!40000 ALTER TABLE `TipoProducto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuario`
--

DROP TABLE IF EXISTS `Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Usuario` (
  `IdUsuario` int NOT NULL AUTO_INCREMENT,
  `IdCliente` int DEFAULT NULL,
  `IdEmpresa` int DEFAULT NULL,
  `LoginName` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Nombre` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `Clave` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `IdContacto` int DEFAULT NULL,
  `Genero` varchar(45) COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `Eliminado` tinyint(1) DEFAULT NULL,
  `IdPerfil` int DEFAULT NULL,
  `IdTipoIdioma` int NOT NULL DEFAULT '1',
  `FechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FechaUltimaSesion` datetime DEFAULT NULL,
  `IdTipoClave` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`IdUsuario`),
  UNIQUE KEY `LoginName_UK` (`LoginName`),
  KEY `Usuario_Cliente_FK` (`IdCliente`),
  KEY `Usuario_LoginName_IDX` (`LoginName`) USING BTREE,
  KEY `Usuario_IdContacto_IDX` (`IdContacto`) USING BTREE,
  KEY `Usuario_Eliminado_IDX` (`Eliminado`) USING BTREE,
  KEY `Usuario_IdPerfil_IDX` (`IdPerfil`) USING BTREE,
  KEY `fk_usr_idioma` (`IdTipoIdioma`),
  KEY `fk_usr_tipoclave` (`IdTipoClave`),
  KEY `fk_usr_emp` (`IdEmpresa`),
  CONSTRAINT `fk_usr_idioma` FOREIGN KEY (`IdTipoIdioma`) REFERENCES `TipoIdioma` (`IdTipoIdioma`),
  CONSTRAINT `Usuario_Cliente_FK` FOREIGN KEY (`IdCliente`) REFERENCES `Cliente` (`IdCliente`),
  CONSTRAINT `Usuario_FK` FOREIGN KEY (`IdPerfil`) REFERENCES `Perfil` (`IdPerfil`),
  CONSTRAINT `Usuario_ibfk_1` FOREIGN KEY (`IdTipoClave`) REFERENCES `TipoClave` (`IdTipoClave`),
  CONSTRAINT `Usuario_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `Empresa` (`IdEmpresa`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuario`
--

LOCK TABLES `Usuario` WRITE;
/*!40000 ALTER TABLE `Usuario` DISABLE KEYS */;
INSERT INTO `Usuario` VALUES (1,1,1,'resolved','Juan E. Vivallos E.','e10adc3949ba59abbe56e057f20f883e',1,'1',0,0,1,'2023-10-11 22:34:15',NULL,2),(2,1,1,'diegomucientes@sergioescobar.cl','Diego Mucientes','b2b39cc714a8a50d5368323d7bd4ba63',2,'1',0,1,1,'2023-10-11 22:34:15',NULL,2),(4,1,1,'andresfarias@sergioescobar.cl','Andrés Farías','96f0eec206056685e584e15654be9a51',3,'1',0,2,1,'2023-10-11 22:34:15',NULL,2),(5,1,1,'juanpablorodriguez@sergioescobar.cl','Juan Pablo Rodríguez','92ce588c49d7e2b931100359ad5a8f2e',4,'1',0,1,1,'2023-10-11 22:34:15',NULL,2),(6,1,1,'nataliamiranda@sergioescobar.cl','Natalia Miranda','168d682c4015678c1032866ab7f8caf0',5,'2',0,3,1,'2023-12-13 00:00:00',NULL,2),(7,1,1,'gersonoyarce@sergioescobar.cl','Gerson Oyarce','5b3d287cc6ede1c6403d79be6cf103d7',6,'1',0,4,1,'2023-12-13 00:00:00',NULL,2),(8,1,1,'krissmartinez@sergioescobar.cl','Kriss Martinez','372ad2e2c834c6d8ea912ca9f33bec8e',7,'2',0,5,1,'2023-12-13 00:00:00',NULL,2),(9,1,1,'benjaminarellano@sergioescobar.cl','Benjamin Arellano','c2b9c37819cab2a5e74130810ec475a8',8,'1',0,6,1,'2023-12-15 00:00:00',NULL,2);
/*!40000 ALTER TABLE `Usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ValorEstilos`
--

DROP TABLE IF EXISTS `ValorEstilos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ValorEstilos` (
  `IdValorEstilo` int NOT NULL AUTO_INCREMENT,
  `IdTipoEstilo` varchar(100) NOT NULL,
  `IdEstilo` int NOT NULL,
  `Color` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`IdValorEstilo`),
  KEY `ValorEstilos_FK` (`IdEstilo`),
  KEY `ValorEstilos_FK_1` (`IdTipoEstilo`),
  CONSTRAINT `ValorEstilos_FK` FOREIGN KEY (`IdEstilo`) REFERENCES `Estilo` (`IdEstilo`),
  CONSTRAINT `ValorEstilos_FK_1` FOREIGN KEY (`IdTipoEstilo`) REFERENCES `TipoEstilo` (`IdTipoEstilo`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ValorEstilos`
--

LOCK TABLES `ValorEstilos` WRITE;
/*!40000 ALTER TABLE `ValorEstilos` DISABLE KEYS */;
INSERT INTO `ValorEstilos` VALUES (19,'PRIMARY_COLOR',1,'#57336e'),(20,'NAVBAR_MENU_BACKGROUND',1,'#323232'),(21,'NAVBAR_MENU_TEXT',1,'#a5a5a5'),(22,'NAVBAR_MENU_ACTIVE_TEXT',1,'#00d7ad'),(23,'NAVBAR_MENU_ARCHIVADA_TEXT',1,'#ff0000'),(24,'MENU_TEXT_TITLE',1,'#c272f7'),(25,'BTN_PRIMARY',1,'#57336e'),(26,'BTN_SECONDARY',1,'#e9e9ed'),(27,'BTN_SUCCESS',1,'#00ba72'),(28,'BTN_WARNING',1,'#ff972f'),(29,'BTN_DANGER',1,'#ff603e'),(30,'BTN_INFO',1,'#1982dd'),(31,'BTN_TEXT_PRIMARY',1,'#ffffff'),(32,'BTN_TEXT_SECONDARY',1,'#ffffff'),(33,'BTN_TEXT_SUCCESS',1,'#ffffff'),(34,'BTN_TEXT_WARNING',1,'#ffffff'),(35,'BTN_TEXT_DANGER',1,'#ffffff'),(36,'BTN_TEXT_INFO',1,'#ffffff');
/*!40000 ALTER TABLE `ValorEstilos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'chexo_net_core'
--
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-16 12:31:54
