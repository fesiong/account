-- MySQL dump 10.13  Distrib 5.7.21, for osx10.13 (x86_64)
--
-- Host: 127.0.0.1    Database: account
-- ------------------------------------------------------
-- Server version	5.7.21-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fe_account`
--

DROP TABLE IF EXISTS `fe_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `message` text,
  `parent_id` int(11) DEFAULT '0' COMMENT '上级id',
  `add_time` int(11) DEFAULT '0',
  `end_time` int(11) DEFAULT '0' COMMENT '结束时间',
  `views` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `password` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_account`
--

LOCK TABLES `fe_account` WRITE;
/*!40000 ALTER TABLE `fe_account` DISABLE KEYS */;
INSERT INTO `fe_account` VALUES (1,'鸟云服务器','IP：12.34.56.78\n端口：22\nssh root@12.34.56.78',0,1526610372,0,0,3,0,1,'123456','account'),(2,'数据库账号','没有附加信息了',1,1526612511,0,0,3,0,1,'qwedfrgtfgthyghtgfredswqsdfred','root');
/*!40000 ALTER TABLE `fe_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fe_admin`
--

DROP TABLE IF EXISTS `fe_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `password` varchar(64) DEFAULT NULL,
  `add_time` int(11) DEFAULT '0',
  `token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_admin`
--

LOCK TABLES `fe_admin` WRITE;
/*!40000 ALTER TABLE `fe_admin` DISABLE KEYS */;
INSERT INTO `fe_admin` VALUES (2,3,'$2y$12$NXRoRElLY0tRbEZRd09wT.ZJRNxZYUHkBcESNiy.zmVCleNwN6pRy',1526629143,NULL);
/*!40000 ALTER TABLE `fe_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fe_article`
--

DROP TABLE IF EXISTS `fe_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `message` text,
  `add_time` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_article`
--

LOCK TABLES `fe_article` WRITE;
/*!40000 ALTER TABLE `fe_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `fe_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fe_category`
--

DROP TABLE IF EXISTS `fe_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `add_time` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_category`
--

LOCK TABLES `fe_category` WRITE;
/*!40000 ALTER TABLE `fe_category` DISABLE KEYS */;
INSERT INTO `fe_category` VALUES (3,'本人账号',NULL,0,0,0,1526353538,1),(2,'网站账号',NULL,0,0,0,1526352527,1),(4,'游戏账号',NULL,0,0,0,1526354016,1);
/*!40000 ALTER TABLE `fe_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fe_user`
--

DROP TABLE IF EXISTS `fe_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_user` (
  `user_name` varchar(32) DEFAULT NULL,
  `add_time` int(11) DEFAULT '0',
  `mobile` bigint(20) DEFAULT '0',
  `email` varchar(32) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `introduction` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `reg_ip` bigint(20) DEFAULT '0',
  `balance` int(11) DEFAULT '0',
  `last_active` int(11) DEFAULT '0',
  `last_login` int(11) DEFAULT '0',
  `group_id` int(11) DEFAULT '0',
  `gender` int(11) DEFAULT '0',
  `province` varchar(16) DEFAULT NULL,
  `city` varchar(16) DEFAULT NULL,
  `county` varchar(16) DEFAULT NULL,
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `token` (`token`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_user`
--

LOCK TABLES `fe_user` WRITE;
/*!40000 ALTER TABLE `fe_user` DISABLE KEYS */;
INSERT INTO `fe_user` VALUES ('超级管理员',1526629143,12345678901,'tpyzlxy@163.com','$2y$12$K1hGSlFHZ21HeW9ib1RocOH8lZm8W.PNW92MunWlvuJ0bHiyHPs9a',NULL,NULL,1,2130706433,0,1526629143,1526629143,1,0,NULL,NULL,NULL,3,NULL);
/*!40000 ALTER TABLE `fe_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fe_user_group`
--

DROP TABLE IF EXISTS `fe_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fe_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(16) DEFAULT NULL,
  `permission` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fe_user_group`
--

LOCK TABLES `fe_user_group` WRITE;
/*!40000 ALTER TABLE `fe_user_group` DISABLE KEYS */;
INSERT INTO `fe_user_group` VALUES (1,'超级管理员组','null');
/*!40000 ALTER TABLE `fe_user_group` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-18 15:40:41
