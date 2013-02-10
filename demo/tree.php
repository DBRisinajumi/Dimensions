<?php
/**
 * administer dimension level tree
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
use \DBRisinajumi\Dimensions\Level;

$sViewMode = isset($_GET['view_mode']) ? $_GET['view_mode'] : 'tree';
$sAction = isset($_GET['action']) ? $_GET['action'] : 'get_tree';

require 'init.php';

if ($sAction == 'add_root') {
    $sCode = $_GET['code'];
    $sLabel = $_GET['label'];
    $oLevel = new Level($Database);
    if (!$oLevel->addLevel(null, 1, $sCode, $sLabel, Level::VISIBLE)) {
        var_dump($oLevel->getErrors());
    }
    unset($oLevel);
}

if ($sViewMode == 'tree') {
    require 'views/full.php';
} elseif ($sViewMode == 'edit') {
    require 'views/form_add_root.php';
}
