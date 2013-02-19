<?php
/**
 * Amount split (ajax)
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
use DBRisinajumi\Dimensions\Period;
use DBRisinajumi\Dimensions\Data;
use DBRisinajumi\Dimensions\Level;

if (!isset($_REQUEST['table_id']) || !isset($_REQUEST['record_id']) || !isset($_REQUEST['amt'])) {
    echo "not all params provided";
    exit;
}
require 'init.php';

$nTableId = (int)$_GET['table_id'];
$nRecordId = (int)$_GET['record_id'];
$nAmt = (double)$_GET['amt'];

$oPeriod = new Period($Database);
$oPeriod->setPeriodType('monthly');
$oData = new Data($Database, $oPeriod);
$oLevel = new Level($Database);

$aDimData = $oData->getDimData($nTableId, $nRecordId);
//var_dump($aDimData);
$sDateFrom = $sDateTo = date($oData->getUserDateFormat());

if (!empty($aDimData['date_from'])) {
    $sDateFrom = date($oData->getUserDateFormat(), strtotime($aDimData['date_from']));
}
if (!empty($aDimData['date_to'])) {
    $sDateTo = date($oData->getUserDateFormat(), strtotime($aDimData['date_to']));
}
$sTitle = "Set levels for items and split sums by periods";
require 'views/header.php';
?>
<form method="post" action="">
<input type="hidden" id="record_id" name="record_id" value="<?=$nRecordId?>"/>
<input type="hidden" id="table_id" name="table_id" value="<?=$nTableId?>"/>
<input type="hidden" id="amt" name="amt" value="<?=$nAmt?>"/>
<table class="dim_table">
    <tr>
        <th>Date from</th>
        <th>Date to</th>
        <th>Level 1</th>
        <th>Level 2</th>
        <th>Level 3</th>
        <th>Action</th>
    </tr>
<tr>
    <td><input name="dim_date_from" value="<?=$sDateFrom?>"/></td>
    <td><input name="dim_date_to" value="<?=$sDateTo?>"/></td>
    <td>
        <select name="dim_l1_id">
        <option value="0">-Select-</option>
<?php
$aLevel = $oLevel->listLevel(null, Level::LEVEL_1);
foreach ($aLevel as $aLevelRecord) {
    if ($aLevelRecord['hidden']) {
        continue;
    }
    $sHtmlSelected = $aDimData['l1_id'] == $aLevelRecord['id'] ? ' selected="selected"' : '';
    ?>
    <option value="<?=$aLevelRecord['id']?>"<?=$sHtmlSelected?>><?=$aLevelRecord['code']?></option>
    <?php
}
?>
        </select>
    </td>
    <td>
        <select name="dim_l2_id">
        <option value="0">-Select-</option>
<?php
$aLevel = $oLevel->listLevel($aDimData['l1_id'], Level::LEVEL_2);
foreach ($aLevel as $aLevelRecord) {
    if ($aLevelRecord['hidden']) {
        continue;
    }
    $sHtmlSelected = $aDimData['l1_id'] == $aLevelRecord['id'] ? ' selected="selected"' : '';
    ?>
    <option value="<?=$aLevelRecord['id']?>"<?=$sHtmlSelected?>><?=$aLevelRecord['code']?></option>
    <?php
}
?>
        </select>
    </td>
    <td>
        <select name="dim_l3_id">
        <option value="0">-Select-</option>
<?php
$aLevel = $oLevel->listLevel($aDimData['l2_id'], Level::LEVEL_3);
foreach ($aLevel as $aLevelRecord) {
    if ($aLevelRecord['hidden']) {
        continue;
    }
    $sHtmlSelected = $aDimData['l1_id'] == $aLevelRecord['id'] ? ' selected="selected"' : '';
    ?>
    <option value="<?=$aLevelRecord['id']?>"<?=$sHtmlSelected?>><?=$aLevelRecord['code']?></option>
    <?php
}
?>
        </select>
    </td>
    <td>
        <input type="submit" name="save" class="button-save" value="Save"/>
    </td>
</tr>
</table>
</form>
<?php
require 'views/footer.php';