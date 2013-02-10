<?php
/**
 * Amount split
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
require 'views/split_header.php';
?>
<table class="dim_table">
    <tr>
        <th>Account holder</th>
        <th>Balance</th>
        <th>Transaction</th>
        <th>Info</th>
        <th>Dimension</th>
    </tr>
    
<?php
require 'init.php';

$oTable = new \DBRisinajumi\Dimensions\Table($Database);
$nTableId = $oTable->getTableIdByName('dim_sample_bank_trans');

$sSql = "SELECT id, acc_holder, balance, amount, message FROM dim_sample_bank_trans";
$q = $Database->query($sSql);

while($row = $q->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['acc_holder']."</td>";
    echo "<td>".$row['balance']."</td>";
    echo "<td>".$row['amount']."</td>";
    echo "<td>".$row['message']."</td>";
    ?>
        <td>
            <a href="split_popup.php?table_id=<?=$nTableId?>&amp;record_id=<?=$row['id']?>&amp;amt=<?=$row['balance']?>" 
               onclick="return popitup('split_popup.php?table_id=<?=$nTableId?>&amp;record_id=<?=$row['id']?>&amp;amt=<?=$row['balance']?>')">Split</a>
        </td>
    <?php
    echo "</tr>";
}
?>
</table>
<?php
require 'views/split_footer.php';
