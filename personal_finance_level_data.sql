/*
SQLyog Ultimate v10.5 
MySQL - 5.5.28-MariaDB : Database - dimensions_test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `dim_l1` */

insert  into `dim_l1`(`id`,`dim_id`,`code`,`label`,`hidden`) values (44,44,'Home','Home',0),(45,45,'Food','Food',0),(46,46,'Finances','Finances',0),(47,47,'Body','Body',0);

/*Data for the table `dim_l2` */

insert  into `dim_l2`(`id`,`l1_id`,`dim_id`,`code`,`label`,`hidden`) values (44,44,44,'Utilities','Utilities',0),(45,44,45,'Household','Household',0),(50,46,50,'Insurance','Insurance',0),(51,46,51,'Savings and investments','Savings and investments',0),(52,46,52,'Loans and financial services','Loans and financial services',0),(53,46,53,'Cash money','Cash money',0),(54,45,54,'Away-from-home food','Away-from-home food',0),(55,45,55,'Home food','Home food',0),(56,47,56,'Health','Health',0),(57,47,57,'Beauty','Beauty',0),(58,47,58,'Clothing','Clothing',0),(59,47,59,'Sports','Sports',0),(60,47,60,'Leisure','Leisure',0);

/*Data for the table `dim_l3` */

insert  into `dim_l3`(`id`,`l2_id`,`dim_id`,`code`,`label`,`hidden`,`levels_table_id`) values (1,44,1,'Electricity','Electricity',0,NULL),(2,44,2,'Water','Water',0,NULL),(3,44,3,'Rent','Rent',0,NULL),(4,44,4,'Phone, Internet, TV, PC','Phone, Internet, TV',0,NULL),(5,44,5,'Gas','Gas',0,NULL),(6,45,6,'Appliances and electronics','Appliances and electronics',0,NULL),(7,45,7,'Construction, garden','Construction, garden',0,NULL),(8,45,8,'Household items','Household items',0,NULL),(11,50,11,'Insurance','Insurance',0,NULL),(12,50,12,'Life Insurance','Life Insurance',0,NULL),(13,50,13,'Non-life insurance','Non-life insurance',0,NULL),(14,51,14,'Investments','Investments',0,NULL),(15,51,15,'Allowances','Allowances',0,NULL),(16,51,16,'Other investments','Other investments',0,NULL),(17,52,17,'Loans and leasing','Loans and leasing',0,NULL),(18,52,18,'Credit repayment','Credit repayment',0,NULL),(19,52,19,'Student loan','Student loan',0,NULL),(20,53,20,'Cash withdrawal','Cash withdrawal',0,NULL),(21,54,21,'Restaurants, cafes','Restaurants, cafes',0,NULL),(22,54,22,'Bars, clubs','Bars, clubs',0,NULL),(23,55,23,'Food','Food',0,NULL),(24,55,24,'Alcohol, tobacco','Alcohol, tobacco',0,NULL),(25,54,25,'Fast food','Fast food',0,NULL),(26,58,26,'Shoes','Shoes',0,NULL),(27,58,27,'Clothes','Clothes',0,NULL),(28,58,28,'Fashion accessories','Fashion accessories',0,NULL),(29,57,29,'Cosmetics','Cosmetics',0,NULL),(30,56,30,'Health care','Health care',0,NULL),(31,56,31,'Pharmaceuticals','Pharmaceuticals',0,NULL),(32,59,32,'Sporting goods','Sporting goods',0,NULL),(33,57,33,'Cosmetic procedures','Cosmetic procedures',0,NULL),(34,59,34,'Other expenses','Other expenses',0,NULL),(35,60,35,'Hobbies','Hobbies',0,NULL),(36,60,36,'Travel','Travel',0,NULL),(37,60,37,'Books, newspapers, magazines','Books, newspapers, magazines',0,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
