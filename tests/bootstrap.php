<?php
$cfg = include dirname(__FILE__).'/../demo/config.php';
require dirname(__FILE__).'/../src/DBRisinajumi/Dimensions/DimAutoload.php';
\DBRisinajumi\Dimensions\DimAutoload::register();
/*$Database = new \mysqli(
    $cfg['db']['host'],
    $cfg['db']['user'],
    $cfg['db']['password'],
    $cfg['db']['database'],
    $cfg['db']['port']
);
$Database->set_charset("utf8");*/
define("TEST_DB_HOST", $cfg['db']['host']);
define("TEST_DB", $cfg['db']['database']);
define("TEST_DB_USER", $cfg['db']['user']);
define("TEST_DB_PASSWORD", $cfg['db']['password']);
