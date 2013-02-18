<?php
/**
 * Amount split
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
$sPageTitle = "Set levels for items and split sums by periods";
require 'views/head.php';
?>
<table class="dim_table">
    <tr>
        <th rowspan="2">Account holder</th>
        <th rowspan="2">Transaction amount</th>
        <th rowspan="2">Info</th>
        <th colspan="6">Dimension</th>
    </tr>
    <tr>
        <th>Date from</th>
        <th>Date to</th>
        <th>Level I</th>
        <th>Level II</th>
        <th>Level III</th>
        <th></th>
    </tr>

    <?php
    require 'init.php';

use DBRisinajumi\Dimensions\Period;
use DBRisinajumi\Dimensions\Data;
use DBRisinajumi\Dimensions\Level;

$oPeriod = new Period($Database);
    $oPeriod->setPeriodType('monthly');
    $oData = new Data($Database, $oPeriod);
    $oLevel = new Level($Database);

    $oTable = new \DBRisinajumi\Dimensions\Table($Database);
    $nTableId = $oTable->getTableIdByName('dim_sample_bank_trans');

    $sSql = "SELECT id, acc_holder, amount, message FROM dim_sample_bank_trans";
    $q = $Database->query($sSql);



    while ($row = $q->fetch_assoc()) {
        $aDimData = $oData->getDimData($nTableId, $row['id']);

        $sDateFrom = $sDateTo = '';

        if (!empty($aDimData['date_from'])) {
            $sDateFrom = date($oData->getUserDateFormat(), strtotime($aDimData['date_from']));
        }
        if (!empty($aDimData['date_to'])) {
            $sDateTo = date($oData->getUserDateFormat(), strtotime($aDimData['date_to']));
        }
        echo "<tr>
            <td>" . $row['acc_holder'] . "</td>
            <td>" . $row['amount'] . "</td>
            <td>" . $row['message'] . "</td>
            <td>" . $sDateFrom . "</td>
            <td>" . $sDateTo . "</td>
            <td>" . $aDimData['l1_code'] . "</td>
            <td>" . $aDimData['l2_code'] . "</td>
            <td>" . $aDimData['l3_code'] . "</td>
                ";
        ?>
        <td>
            <a href="split_popup.php?table_id=<?= $nTableId ?>&amp;record_id=<?= $row['id'] ?>&amp;amt=<?= $row['amount'] ?>"
               onclick="return popitup('split_popup.php?table_id=<?= $nTableId ?>&amp;record_id=<?= $row['id'] ?>&amp;amt=<?= $row['amount'] ?>')">Set</a>
        </td>
        <?php
        echo "</tr>";
    }
    ?>
</table>
<script type="text/javascript" src="js/dim_split.js"></script>
<?php
require 'views/footer.php';
