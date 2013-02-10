<?php
$cfg = include 'config.php';
require '../src/DBRisinajumi/Dimensions/DimAutoload.php';
\DBRisinajumi\Dimensions\DimAutoload::register();
$Database = new \mysqli(
    $cfg['db']['host'],
    $cfg['db']['user'],
    $cfg['db']['password'],
    $cfg['db']['database'],
    $cfg['db']['port']
);
$Database->set_charset("utf8");

