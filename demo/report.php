<?php
/**
 * Report examples
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
require 'init.php';

$nLevel = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$nParentLevelId = isset($_GET['parent_level_id']) ? (int)$_GET['parent_level_id'] : null;

$Report = new \DBRisinajumi\Dimensions\ReportExample($Database);
$Report->setPeriodType('monthly');
$Report->setLevel($nLevel, $nParentLevelId);
$aBreadcrumbs = $Report->getBreadcrumbs($nLevel, $nParentLevelId);
$sTitle = "Report examples";
require 'views/header.php';
?>
<?php if (!empty($bShowButton)) { ?>
    <input type="button" value="back" class="button-cancel" onclick="window.history.back();return false;"/>
<?php } ?>
    <ul id="dim_breadcrumb">
        <li><a href="">Reports</a></li>
<?php
if (!empty($aBreadcrumbs)) {
    foreach($aBreadcrumbs as $aBreadcrumb) {
?>
    <li>
    &lt; <a href="?level=<?=$aBreadcrumb['level']?>&amp;parent_level_id=<?=
    $aBreadcrumb['parent_level_id']?>"><?=$aBreadcrumb['code']?></a>
    </li>
<?php
    }
}
?>
    </ul>
    <div style="clear:both;width:100%;"></div>

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
echo "<h1>Dates horizontally</h1>";
$Report->createGridDataHorizontalDates();

echo "<h1>Dates vertically</h1>";
$Report->createGridData();

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
require 'views/footer.php';