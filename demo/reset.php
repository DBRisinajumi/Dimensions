<?php
/**
 * Reset SQL database
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */

require 'init.php';

$sSql = "TRUNCATE TABLE dim_l1;TRUNCATE TABLE dim_l2;TRUNCATE TABLE dim_l3;";
$Database->multi_query($sSql);
$sSql = file_get_contents(dirname(__FILE__)."/../personal_finance_level_data.sql");
$Database->multi_query($sSql);

header('Location: index.php');
exit;