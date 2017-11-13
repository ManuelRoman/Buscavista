-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: buscavista
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `entradas`
--

DROP TABLE IF EXISTS `entradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entradas` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) DEFAULT NULL,
  `contenido` text,
  `imagen1` varchar(255) DEFAULT NULL,
  `latitud` float(10,7) DEFAULT NULL,
  `longitud` float(10,7) DEFAULT NULL,
  `preferencia` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entradas`
--

LOCK TABLES `entradas` WRITE;
/*!40000 ALTER TABLE `entradas` DISABLE KEYS */;
INSERT INTO `entradas` VALUES (1,'Repuestos Valme','Tienda de repuestos automovilísticos\r\nDirección: Av. de Bellavista, 143, 41014 Sevilla\r\nTeléfono: 954 69 18 70','repuestos_valme.png',37.3199463,-5.9685040,0),(2,'Ermita de Valme','La Ermita de Valme fue edificada, según la tradición, por mandato del rey Fernando \r\nIII el Santo, sobre el cerro o cortijo de Cuartos, como muestra de agradecimiento a la Virgen María por el valimiento que en dicho lugar le otorgó\r\ndurante la conquista de Sevilla.\r\nAunque son muy escasos los testimonios documentales que existen sobre esta\r\n primitiva capilla, podemos aventurar que debió de ser pequeña y sencilla, casi\r\n un oratorio, exclusivamente destinado a albergar a la imagen \r\ngótica invocada por San Fernando, que recibió el nombre de Valme en recuerdo\r\n de la súplica del monarca. \r\nJunto a Ella, el Rey Santo colocaría el pendón arrebatado a los musulmanes de \r\nIsbilia, tal y como había prometido a la Señora cuando solicitó su auxilio en la \r\ncontienda.\r\nCon el tiempo, a la capilla originaria se le fueron adosando nuevas construcciones, \r\ndestinadas a albergar al santero y a los peregrinos que hasta allí llegaban para\r\n venerar a la Santísima Virgen, especialmente el día de su fiesta,  que se\r\n celebraba entonces el segundo día de la Pascua de Pentecostés.\r\nA través de los Anales de Sevilla recopilados por Diego Ortiz de Zúñiga se tiene \r\nnoticia de que el 27 de octubre de 1667, \r\na las cuatro de la tarde, volaron la casa y molino de la pólvora, que estaban a \r\nuna legua de la ciudad,  «junto a la ermita de Nuestra Señora del Valme, donde tuvo sus alojamientos y reales nuestro conquistador San Fernando».','ermita1.jpg',37.3257141,-5.9736381,2),(3,'Loterias Asencio y Toledo','Administación de loterias en la calle Asencio y Toledo','loteria_asencio.png',37.3248329,-5.9684248,0),(4,'Parroquia del Sagrado Corazón','Considerado uno de los edificios más singulares de Bellavista, las obras de la parroquia, bajo diseño del arquitecto Aurelio Gómez Millán, se iniciaron en el año 1942, contribuyendo a su construcción muchos vecinos del barrio con sus propias manos. Se concluyó en 1948 aunque no fue hasta 1951 cuando fue erigida como parroquia.','1510508584.jpeg',37.3247147,-5.9675846,0),(5,'Dulce Nombre de Bellavista','Contacto Parroquia del Dulce Nombre\r\n\r\nTeléfono de contacto: 954 69 04 74\r\nDirección postal: C/ Caldereros nº20; cp 41014 Sevilla\r\n\r\nEl martes 18 de Junio de 1968, se celebra en Sevilla el primer día del VII\r\n Congreso Eucarístico Nacional, cuya jornada inicial estaba dedicada a la \r\n“Comunidad universal. Pueblo de Dios”. Precisamente en este día –entre \r\nfulgores de Eucaristía- nuestro eminentísimo prelado Don José María Bueno \r\nMonreal, Cardenal – Arzobispo de Sevilla, firmaba el decreto ereccional de la \r\nnueva parroquia del Dulce Nombre de María, en la barriada sevillana de \r\nBellavista.','1510509271.png',37.3196983,-5.9678593,1),(6,'Pastelería Carolina','Calle Rosas, 12, 41014 Sevilla\r\nTeléfono: 954 69 29 44\r\nAbre a las 10:30','1510511627.png',37.3237114,-5.9662795,0),(7,'Hotel Bellavista Sevilla','Av. de Bellavista, 153, 41014 Sevilla\r\nTeléfono: 954 69 35 00\r\nEste sencillo hotel, situado a 7 minutos a pie de la estación de tren de \r\nBellavista, se encuentra a 8 km de la Torre del Oro y a 10 km del Real Alcázar \r\nde Sevilla.','1510511873.png',37.3187752,-5.9685082,0),(8,'Hotel Doña Carmela','Av. de Jerez, 72, 41014 Sevilla\r\nTeléfono: 954 69 29 03\r\nEste hotel informal se encuentra a 9 km del Real Alcázar y de la catedral de \r\nSevilla, así como a 17 km del aeropuerto.\r\nLas habitaciones, sencillas y modernas, cuentan con Wi-Fi gratis, televisión de\r\n pantalla plana y escritorio. Las suites incluyen sala de estar. Servicio de \r\nhabitaciones disponible.','1510512088.png',37.3270721,-5.9664598,0),(9,'Centro de salud Bellavista','Av. de Jerez, 67, 41014 Sevilla\r\nTeléfono: 954 71 21 52','1510512270.png',37.3254890,-5.9650674,0),(10,'Azcona Bar · Restaurante','Calle Gaspar Calderas, 11, 41014 Sevilla\r\nTeléfono: 954 69 27 63','1510595469.png',37.3244400,-5.9697657,0),(11,'Bar Herencia El Tranvi','Café, Cerveza fría, tapas gratis algunos fines de samana.\r\nTeléfono: 651 65 26 04','1510595770.png',37.3249054,-5.9689913,0),(12,'Hordeum','Plaza Fernando VI, 13 (Bellavista), 41014 Sevilla\r\nTeléfono: 954 69 37 82','1510596182.png',37.3256912,-5.9669552,0),(13,'Oficina Seguros MAPFRE','Plaza Fernando VI, 14, 41014 Sevilla\r\nTeléfono: 954 69 23 62','1510596372.png',37.3255539,-5.9670067,0),(14,'FARMACIA CENTRO BELLAVISTA','Calle Asensio y Toledo, 40, 41014 Sevilla\r\nTeléfono: 954 69 09 24','1510596510.png',37.3251534,-5.9682665,0);
/*!40000 ALTER TABLE `entradas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entradas_etiquetas`
--

DROP TABLE IF EXISTS `entradas_etiquetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entradas_etiquetas` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `entrada_id` int(255) NOT NULL,
  `etiqueta_id` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_entradas` (`entrada_id`),
  KEY `fk_etiquetas` (`etiqueta_id`),
  CONSTRAINT `fk_entradas` FOREIGN KEY (`entrada_id`) REFERENCES `entradas` (`id`),
  CONSTRAINT `fk_etiquetas` FOREIGN KEY (`etiqueta_id`) REFERENCES `etiquetas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entradas_etiquetas`
--

LOCK TABLES `entradas_etiquetas` WRITE;
/*!40000 ALTER TABLE `entradas_etiquetas` DISABLE KEYS */;
INSERT INTO `entradas_etiquetas` VALUES (5,3,4),(6,3,5),(13,4,6),(14,4,7),(15,4,8),(43,2,1),(44,2,3),(48,5,9),(49,5,6),(50,5,7),(51,6,10),(52,6,11),(53,6,12),(54,7,13),(55,7,14),(56,7,15),(57,8,13),(58,8,14),(59,8,16),(60,9,17),(61,9,18),(62,9,19),(65,10,20),(66,10,21),(67,10,22),(68,10,23),(69,10,24),(70,10,25),(71,10,26),(72,11,27),(73,11,21),(74,11,26),(75,12,20),(76,12,21),(77,12,28),(78,12,26),(79,12,27),(80,13,29),(81,13,30),(82,13,31),(83,14,32),(84,14,33),(85,14,34),(86,1,1),(87,1,2);
/*!40000 ALTER TABLE `entradas_etiquetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etiquetas`
--

DROP TABLE IF EXISTS `etiquetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etiquetas` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `estadistica` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etiquetas`
--

LOCK TABLES `etiquetas` WRITE;
/*!40000 ALTER TABLE `etiquetas` DISABLE KEYS */;
INSERT INTO `etiquetas` VALUES (1,'valme',5),(2,'repuestos',1),(3,'ermita',2),(4,'loterias',0),(5,'asencio',0),(6,'iglesia',4),(7,'misa',0),(8,'templo',0),(9,'parroquia',0),(10,'pasteleria',0),(11,'pasteles',0),(12,'dulces',1),(13,'hotel',1),(14,'habitaciones',0),(15,'habitacion',0),(16,'pernoctar',0),(17,'consultorio',0),(18,'medico',1),(19,'consulta',0),(20,'bar',0),(21,'cerveza',2),(22,'vino',0),(23,'licores',0),(24,'comer',0),(25,'cenar',0),(26,'tapas',0),(27,'cafe',1),(28,'camida',0),(29,'seguro',0),(30,'seguros',0),(31,'mapfre',0),(32,'farmacia',1),(33,'medicamentos',0),(34,'medicina',1);
/*!40000 ALTER TABLE `etiquetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `role` varchar(55) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `PASSWORD` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'ROLE_ADMIN','admin','admin','admin@a.com','$2a$06$Jh3Li/Y0cBva6QKmuM64A.KBYIckiVeHU6zgJU/j3gPbR0qrnr95u'),(2,'ROLE_SUPER_ADMIN','superadmin','superadmin','superadmin@a.com','$2a$06$VTMO0OQC8/rqTumOuowuJusCSxanPR0xNEPyOPz5PLpBhEmFcGrlK');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-13 20:23:13
