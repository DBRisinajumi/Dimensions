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
    TRUNCATE TABLE dim_l2;
    TRUNCATE TABLE dim_l3;
    TRUNCATE TABLE dim_table;
    TRUNCATE TABLE dim_data;
    TRUNCATE TABLE dim_period;
    TRUNCATE TABLE dim_data_period;
    TRUNCATE TABLE dim_sample_bank_trans;
";
$Database->multi_query($sSql);
$sSql = file_get_contents(dirname(__FILE__)."/../personal_finance_level_data.sql");
$Database->multi_query($sSql);

header('Location: index.php');
exit;