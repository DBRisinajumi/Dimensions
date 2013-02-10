<?php
/**
 * Report examples
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Report examples</title>
    </head>
<body>
<?php if (!empty($bShowButton)) { ?>
    <input type="button" value="back" class="button-cancel" onclick="window.history.back();return false;"/>
<?php } ?>
<div id="paging">
<?php if (!empty($prev_year)) { ?>
    <a class="page-left" title="previous year" 
    href="/?year=<?=$prev_year?>" 
    title="<tmpl_var name="prev_year">"></a>
<?php } ?>
<?php if (!empty($next_year)) { ?>
    <a class="page-right" title="next year" 
    href="/?year=<?=$next_year?>" 
    title="<?=$next_year?>"></a>
<?php } ?>
</div>
<?php
require 'init.php';

$Report = new \DBRisinajumi\Dimensions\ReportExample($Database);
$Report->setPeriodType('monthly');

$nLevel = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$nParentLevelId = isset($_GET['parent_level_id']) ? (int)$_GET['parent_level_id'] : null;

$Report->setLevel($nLevel, $nParentLevelId);

echo "<h1>Dates vertically</h1>";
$Report->createGridData();

echo "<h1>Dates horizotnally</h1>";
$Report->createGridDataHorizontalDates();
if (isset($_GET['period_id'])) {
    $aItems = $Report->createListData($_GET['period_id'], $_GET['level'], $_GET['level_id']);
    echo '<ol>';
    foreach ($aItems as $aItem) {
        echo '<li>table:'.$aItem['table_name'].',
        record: '.$aItem['record_id'].',
        sum:  '.$aItem['period_amt'].' ('.$aItem['amt'].')</li>';
    }
    echo '</ol>';
}
?>
</body>
</html>