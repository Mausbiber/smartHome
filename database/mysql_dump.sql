-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: localhost    Database: smartHome
-- ------------------------------------------------------
-- Server version	5.5.47

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
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `ip_UNIQUE` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedulers`
--

DROP TABLE IF EXISTS `schedulers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedulers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `switches_id` int(11) NOT NULL,
  `date_start_on` timestamp NULL DEFAULT NULL,
  `date_start_off` timestamp NULL DEFAULT NULL,
  `date_stop` tinyint(1) DEFAULT NULL,
  `date_stop_on` timestamp NULL DEFAULT NULL,
  `date_stop_off` timestamp NULL DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `interval_number` int(11) DEFAULT NULL,
  `interval_unit` varchar(45) DEFAULT NULL,
  `weekly_monday` tinyint(1) DEFAULT NULL,
  `weekly_tuesday` tinyint(1) DEFAULT NULL,
  `weekly_wednesday` tinyint(1) DEFAULT NULL,
  `weekly_thursday` tinyint(1) DEFAULT NULL,
  `weekly_friday` tinyint(1) DEFAULT NULL,
  `weekly_saturday` tinyint(1) DEFAULT NULL,
  `weekly_sunday` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`,`switches_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`),
  KEY `fk_schedulers_switches_idx` (`switches_id`),
  CONSTRAINT `fk_schedulers_switches` FOREIGN KEY (`switches_id`) REFERENCES `switches` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedulers`
--

LOCK TABLES `schedulers` WRITE;
/*!40000 ALTER TABLE `schedulers` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedulers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensor_data`
--

DROP TABLE IF EXISTS `sensor_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensor_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `data` float DEFAULT NULL,
  `sensors_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`sensors_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_sensor_data_sensors1_idx` (`sensors_id`),
  CONSTRAINT `fk_sensor_data_sensors1` FOREIGN KEY (`sensors_id`) REFERENCES `sensors` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor_data`
--

LOCK TABLES `sensor_data` WRITE;
/*!40000 ALTER TABLE `sensor_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensor_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensor_types`
--

DROP TABLE IF EXISTS `sensor_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensor_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) DEFAULT NULL,
  `description` varchar(60) DEFAULT NULL,
  `icon` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor_types`
--

LOCK TABLES `sensor_types` WRITE;
/*!40000 ALTER TABLE `sensor_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensor_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensors`
--

DROP TABLE IF EXISTS `sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `clients_id` int(11) NOT NULL,
  `sensor_types_id` int(11) NOT NULL,
  `argA` varchar(45) DEFAULT NULL,
  `argB` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`,`clients_id`,`sensor_types_id`),
  KEY `fk_sensors_clients1_idx` (`clients_id`),
  KEY `fk_sensors_sensor_types1_idx` (`sensor_types_id`),
  CONSTRAINT `fk_sensors_sensor_types1` FOREIGN KEY (`sensor_types_id`) REFERENCES `sensor_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_sensors_clients1` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensors`
--

LOCK TABLES `sensors` WRITE;
/*!40000 ALTER TABLE `sensors` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `scheduler_preview_period` int(11) NOT NULL,
  `scheduler_preview_items` int(11) NOT NULL,
  `scheduler_settings_page_per_view` int(11) NOT NULL,
  PRIMARY KEY (`scheduler_preview_period`,`scheduler_preview_items`,`scheduler_settings_page_per_view`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (8,100,4);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switch_types`
--

DROP TABLE IF EXISTS `switch_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) DEFAULT NULL,
  `description` varchar(60) DEFAULT NULL,
  `icon` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switch_types`
--

LOCK TABLES `switch_types` WRITE;
/*!40000 ALTER TABLE `switch_types` DISABLE KEYS */;
INSERT INTO `switch_types` VALUES (16,'raspi_gpio','Raspberry Pi GPIO Ports','raspi2.png'),(17,'sis_usb_socket','per USB schaltbare Steckdosenleiste - SIS','usb2.png'),(18,'tf_dual','Tinkerforge Dual Relay (Relais)	','relay2.png'),(19,'tf_remote','Tinkerforge RemoteSwitch Bricklet (Funksender)','transmitter2.png'),(20,'tf_ind_quad_relay','Tinkerforge Industrial Quad Relay','relay2.png');
/*!40000 ALTER TABLE `switch_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switches`
--

DROP TABLE IF EXISTS `switches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `clients_id` int(11) NOT NULL,
  `switch_types_id` int(11) NOT NULL,
  `argA` varchar(15) DEFAULT NULL,
  `argB` varchar(15) DEFAULT NULL,
  `argC` varchar(15) DEFAULT NULL,
  `argD` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`,`clients_id`,`switch_types_id`),
  KEY `fk_switches_clients1_idx` (`clients_id`),
  KEY `fk_switches_switch_types1_idx` (`switch_types_id`),
  CONSTRAINT `fk_switches_clients1` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_switches_switch_types1` FOREIGN KEY (`switch_types_id`) REFERENCES `switch_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switches`
--

LOCK TABLES `switches` WRITE;
/*!40000 ALTER TABLE `switches` DISABLE KEYS */;
/*!40000 ALTER TABLE `switches` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-02-08 22:08:57
