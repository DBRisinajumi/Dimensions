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
<?php }

$sHtmlBredCrumb = '
    <ul id="dim_breadcrumb">
        <li><a href="report.php">Reports</a></li>
     ';
if (!empty($aBreadcrumbs)) {
    foreach($aBreadcrumbs as $aBreadcrumb) {
        $sHtmlBredCrumb .= '
    <li>
    &lt; <a href="?level='.$aBreadcrumb['level'].
                '&amp;parent_level_id=' .  $aBreadcrumb['parent_level_id']
         . '">' . $aBreadcrumb['code'] . '</a>
    </li>
    ';
    }
}

$sHtmlBredCrumb .= '</ul><br />';

?>

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
echo $sHtmlBredCrumb;
$Report->createGridDataHorizontalDates();

//echo "<h1>Dates vertically</h1>";
//echo $sHtmlBredCrumb;
//$Report->createGridData();

if (isset($_GET['period_id'])) {

    $aItemsLabel = array();
    $aItemsLabel['period_label'] = $Report->getPeriodLabel($_GET['period_id']);
    $aItemsLabel['level_code'] = $Report->getLevelCode($_GET['level'], $_GET['level_id']);

    $aItems = $Report->createListData($_GET['period_id'], $_GET['level'], $_GET['level_id']);
    ?>
    <br />
    <table id="dimension_table" border="1" class="dim_table">
        <tr>
            <th colspan="7">
                Items: Period <?=$aItemsLabel['period_label']?> / Level <?=$aItemsLabel['level_code']?>
            </th>
        </tr>
        <tr>
            <th>Doc number</th>
            <th>RecordId</th>
            <th>Type</th>
            <th>Date</th>
            <th>Descr</th>
            <th>Period Amunt</th>
            <th>Full Amunt</th>
        </tr>

        <?
    foreach ($aItems as $aItem) {
        ?>
        <tr>
            <td><?=$aItem['doc_number']?></td>
            <td><?=$aItem['doc_id']?></td>
            <td><?=$aItem['doc_type']?></td>
            <td><?=$aItem['doc_date']?></td>
            <td><?=$aItem['doc_item_descr']?></td>
            <td><?=$aItem['period_amt']?></td>
            <td><?=$aItem['amt']?></td>

        </tr>
        <?
    }
?></table><?
}
require 'views/footer.php';