<?php
/**
 * Reset SQL database
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */

require 'init.php';

$sSql = "TRUNCATE TABLE dim_l1";
$Database->query($sSql);
$sSql = "TRUNCATE TABLE dim_l2";
$Database->query($sSql);
$sSql = "TRUNCATE TABLE dim_l3";
$Database->query($sSql);

$Database->multi_query(file_get_contents(dirname(__FILE__)."/../personal_finance_level_data.sql"));

header('Location: index.php');
exit;