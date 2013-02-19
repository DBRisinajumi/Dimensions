<?php
/**
 * Reset SQL database
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */

require 'init.php';

$sSql = "
    TRUNCATE TABLE dim_l1;
    INSERT  INTO `dim_l1`(`id`,`dim_id`,`code`,`label`,`hidden`) VALUES (44,44,'Home','Home',0),(45,45,'Food','Food',0),(46,46,'Finances','Finances',0),(47,47,'Body','Body',0);

    TRUNCATE TABLE dim_l2;
    INSERT  INTO `dim_l2`(`id`,`l1_id`,`dim_id`,`code`,`label`,`hidden`) VALUES (44,44,44,'Utilities','Utilities',0),(45,44,45,'Household','Household',0),(50,46,50,'Insurance','Insurance',0),(51,46,51,'Savings and investments','Savings and investments',0),(52,46,52,'Loans and financial services','Loans and financial services',0),(53,46,53,'Cash money','Cash money',0),(54,45,54,'Away-from-home food','Away-from-home food',0),(55,45,55,'Home food','Home food',0),(56,47,56,'Health','Health',0),(57,47,57,'Beauty','Beauty',0),(58,47,58,'Clothing','Clothing',0),(59,47,59,'Sports','Sports',0),(60,47,60,'Leisure','Leisure',0);

    TRUNCATE TABLE dim_l3;
    INSERT  INTO `dim_l3`(`id`,`l2_id`,`dim_id`,`code`,`label`,`hidden`,`levels_table_id`) VALUES (1,44,1,'Electricity','Electricity',0,NULL),(2,44,2,'Water','Water',0,NULL),(3,44,3,'Rent','Rent',0,NULL),(4,44,4,'Phone, Internet, TV, PC','Phone, Internet, TV',0,NULL),(5,44,5,'Gas','Gas',0,NULL),(6,45,6,'Appliances and electronics','Appliances and electronics',0,NULL),(7,45,7,'Construction, garden','Construction, garden',0,NULL),(8,45,8,'Household items','Household items',0,NULL),(11,50,11,'Insurance','Insurance',0,NULL),(12,50,12,'Life Insurance','Life Insurance',0,NULL),(13,50,13,'Non-life insurance','Non-life insurance',0,NULL),(14,51,14,'Investments','Investments',0,NULL),(15,51,15,'Allowances','Allowances',0,NULL),(16,51,16,'Other investments','Other investments',0,NULL),(17,52,17,'Loans and leasing','Loans and leasing',0,NULL),(18,52,18,'Credit repayment','Credit repayment',0,NULL),(19,52,19,'Student loan','Student loan',0,NULL),(20,53,20,'Cash withdrawal','Cash withdrawal',0,NULL),(21,54,21,'Restaurants, cafes','Restaurants, cafes',0,NULL),(22,54,22,'Bars, clubs','Bars, clubs',0,NULL),(23,55,23,'Food','Food',0,NULL),(24,55,24,'Alcohol, tobacco','Alcohol, tobacco',0,NULL),(25,54,25,'Fast food','Fast food',0,NULL),(26,58,26,'Shoes','Shoes',0,NULL),(27,58,27,'Clothes','Clothes',0,NULL),(28,58,28,'Fashion accessories','Fashion accessories',0,NULL),(29,57,29,'Cosmetics','Cosmetics',0,NULL),(30,56,30,'Health care','Health care',0,NULL),(31,56,31,'Pharmaceuticals','Pharmaceuticals',0,NULL),(32,59,32,'Sporting goods','Sporting goods',0,NULL),(33,57,33,'Cosmetic procedures','Cosmetic procedures',0,NULL),(34,59,34,'Other expenses','Other expenses',0,NULL),(35,60,35,'Hobbies','Hobbies',0,NULL),(36,60,36,'Travel','Travel',0,NULL),(37,60,37,'Books, newspapers, magazines','Books, newspapers, magazines',0,NULL);

    TRUNCATE TABLE dim_table;
    INSERT  INTO `dim_table`(`id`,`table_type`,`label`,`table_name`,`table_id_field`,`report_select`,`level_select`) VALUES (1,'transactions',NULL,'dim_sample_bank_trans','id',NULL,NULL);
    
    TRUNCATE TABLE dim_data;
    INSERT  INTO `dim_data`(`id`,`table_id`,`record_id`,`l1_id`,`l2_id`,`l3_id`,`amt`,`date_from`,`date_to`) VALUES (22,1,4,45,55,23,3000,'2013-02-16 17:25:15','2013-02-16 17:25:15'),(23,1,5,45,54,21,1000,'2013-02-16 17:25:30','2013-02-16 17:25:30'),(24,1,6,44,45,6,29900,'2013-02-16 17:25:52','2013-02-16 17:25:52'),(25,1,7,47,58,26,1800,'2013-02-16 17:26:08','2013-02-16 17:26:08'),(26,1,8,44,44,1,14000,'2013-03-01 17:26:30','2013-03-31 17:26:30'),(27,1,9,46,50,13,30000,'2013-01-01 17:27:03','2013-12-31 17:27:03');

    TRUNCATE TABLE dim_period;
    INSERT  INTO `dim_period`(`id`,`period_type`,`date_from`,`date_to`) VALUES (5,'monthly','2013-02-01 17:25:15','2013-03-01 17:25:15'),(6,'monthly','2013-03-01 17:26:30','2013-04-01 17:26:30'),(7,'monthly','2013-01-01 17:27:03','2013-02-01 17:27:03'),(8,'monthly','2013-03-01 17:25:15','2013-04-01 17:25:15'),(9,'monthly','2013-04-01 17:26:30','2013-05-01 17:26:30'),(10,'monthly','2013-05-01 17:26:30','2013-06-01 17:26:30'),(11,'monthly','2013-06-01 17:26:30','2013-07-01 17:26:30'),(12,'monthly','2013-07-01 17:26:30','2013-08-01 17:26:30'),(13,'monthly','2013-08-01 17:26:30','2013-09-01 17:26:30'),(14,'monthly','2013-09-01 17:26:30','2013-10-01 17:26:30'),(15,'monthly','2013-10-01 17:26:30','2013-11-01 17:26:30'),(16,'monthly','2013-11-01 17:26:30','2013-12-01 17:26:30'),(17,'monthly','2013-12-01 17:26:30','2014-01-01 17:26:30');
    
    TRUNCATE TABLE dim_data_period;
    INSERT  INTO `dim_data_period`(`id`,`dim_data_id`,`period_id`,`period_amt`,`l1_id`,`l2_id`,`l3_id`) VALUES (21,22,5,3000,45,55,23),(22,23,5,1000,45,54,21),(23,24,5,29900,44,45,6),(24,25,5,1800,47,58,26),(25,26,6,14000,44,44,1),(26,27,7,2554,46,50,13),(27,27,5,2307,46,50,13),(28,27,8,2554,46,50,13),(29,27,6,0,46,50,13),(30,27,9,2472,46,50,13),(31,27,10,2554,46,50,13),(32,27,11,2472,46,50,13),(33,27,12,2554,46,50,13),(34,27,13,2554,46,50,13),(35,27,14,2472,46,50,13),(36,27,15,2554,46,50,13),(37,27,16,2472,46,50,13),(38,27,17,2481,46,50,13);
    
    TRUNCATE TABLE dim_sample_bank_trans;
    INSERT  INTO `dim_sample_bank_trans`(`id`,`acc_holder`,`amount`,`message`) VALUES (4,'John Doe',30.99,'Supermarket'),(5,'John Doe',10.5,'TacoBurgers'),(6,'John Doe',299.99,'Consumer electronics'),(7,'John Doe',18,'Dressman'),(8,'John Doe',140.46,'Electricity bill'),(9,'John Doe',300,'Car insurance');
";
$Database->multi_query($sSql);
$sSql = file_get_contents(dirname(__FILE__)."/../personal_finance_level_data.sql");
$Database->multi_query($sSql);

header('Location: index.php');
exit;