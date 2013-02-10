/*
SQLyog Ultimate v10.5 
MySQL - 5.5.28-MariaDB : Database - dim_demo
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `dim_data` */

DROP TABLE IF EXISTS `dim_data`;

CREATE TABLE `dim_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_id` int(11) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `l1_id` int(11) DEFAULT NULL,
  `l2_id` int(11) DEFAULT NULL,
  `l3_id` int(11) DEFAULT NULL,
  `amt` decimal(10,0) DEFAULT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_data_period` */

DROP TABLE IF EXISTS `dim_data_period`;

CREATE TABLE `dim_data_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dim_data_id` int(11) DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `period_amt` decimal(10,0) DEFAULT NULL,
  `l1_id` int(11) DEFAULT NULL,
  `l2_id` int(11) DEFAULT NULL,
  `l3_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_l1` */

DROP TABLE IF EXISTS `dim_l1`;

CREATE TABLE `dim_l1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dim_id` int(11) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_l2` */

DROP TABLE IF EXISTS `dim_l2`;

CREATE TABLE `dim_l2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `l1_id` int(11) DEFAULT NULL,
  `dim_id` int(11) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_l3` */

DROP TABLE IF EXISTS `dim_l3`;

CREATE TABLE `dim_l3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `l2_id` int(11) DEFAULT NULL,
  `dim_id` int(11) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `levels_table_id` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_period` */

DROP TABLE IF EXISTS `dim_period`;

CREATE TABLE `dim_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period_type` enum('weekly','monthly') DEFAULT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `dim_sample_bank_trans` */

DROP TABLE IF EXISTS `dim_sample_bank_trans`;

CREATE TABLE `dim_sample_bank_trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_holder` varchar(255) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `dim_table` */

DROP TABLE IF EXISTS `dim_table`;

CREATE TABLE `dim_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_type` enum('transactions','levels') CHARACTER SET ascii DEFAULT 'transactions',
  `label` varchar(20) DEFAULT NULL,
  `table_name` varchar(255) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `table_id_field` varchar(20) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `report_select` text,
  `level_select` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
