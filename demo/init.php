<?php
$cfg = include 'config.php';
require '../src/Dimension/DimAutoload.php';
DimAutoload::register();
$Database = new \mysqli(
    $cfg['db']['host'],
    $cfg['db']['user'],
    $cfg['db']['password'],
    $cfg['db']['database'],
    $cfg['db']['port']
);
$Database->set_charset("utf8");

