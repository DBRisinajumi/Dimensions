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
    $sPageTitle = "Define levels";
    require 'views/head.php';
    ?>
    <a href="?view_mode=edit">Add root level</a>
<table class="dim_table">
    <tr>
        <td id="dim_tree"></td>
        <td id="dim_form"></td>
    </tr>
</table>
    
        <script type="text/javascript" src="js/jstree/jquery.jstree.js"></script>
        <script type="text/javascript" src="js/dim_tree.js"></script>
     <?
} elseif ($sViewMode == 'edit') {
        $sPageTitle = "Add root level levels";
    require 'views/head.php';
    require 'views/form_add_root.php';
?>
        <script type="text/javascript" src="js/dim_tree.js"></script>
    <?
}
require 'views/footer.php';
