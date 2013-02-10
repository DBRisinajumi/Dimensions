<?php
namespace DBRisinajumi\Dimensions;

/**
 * ReportExample class
 *
 * @author Juris Malinens <juris.malinens@inbox.lv>
 */
class ReportExample extends Report
{
    /**
     * create new ReportExample instance
     * 
     * @param \mysqli $Database
     */
    public function __construct(\mysqli $Database)
    {
        parent::__construct($Database);
    }
    /**
     * creates simple example table with periods as rows
     * 
     * @return array
     */
    public function createGridData()
    {
        $aPeriods = $this->getGridDataPeriods();
        $aLevels = $this->getGridDataLevels();
        if (empty($aPeriods) || empty($aLevels)) {
            $this->aErrors[] = 'No period or level data provided';

            return false;
        }
        $aAllPeriodLevels = $this->getAllGridDataPeriodLevels($aPeriods, $aLevels);
        ?>
        <table id="dimension_table" border="1" class="dim_table">
        <tbody><tr>
            <th></th>
        <?php
        foreach ($aLevels as $nLevelId => $aLevel) {
            ?>
            <th>
            <?php
            if (!empty($aLevel['link_exists'])) {
            ?>
                <a class="more" title="<?=$aLevel['label']?>" 
               href="?year=<?=$aLevel['nYear']?>&amp;level=<?=$aLevel['level']
               ?>&amp;parent_level_id=<?=$aLevel['level_id']?>"><?=$aLevel['code']?></a>
            <?php
            } else {
                echo $aLevel['code'];
            }
            ?>
            </th>
            <?php
        }
        ?>
        </tr></tbody>
        <?php
        foreach ($aPeriods as $nPeriodId => $aPeriod) {
            echo "<tr><td>{$aPeriod['period_name_x_axis']}</td>\n";
            foreach ($aLevels as $aLevel) {
                $nLevelId = $aLevel['id'];
                ?>
                <td>
                <?php
                if (!empty($aAllPeriodLevels[$nPeriodId][$nLevelId]['link_exists'])) {
                ?>
                    <a class="more" title="<?=$aLevel['label']?>" 
                   href="?year=<?=$aLevel['nYear']?>&amp;period_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['period_id']?>&amp;level=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['level']?>&amp;parent_level_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['parent_level_id']?>&amp;level_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['level_id']?>"><?=
                   $this->getFormattedAmt($aAllPeriodLevels[$nPeriodId][$nLevelId]['amt'])?></a>
                <?php
                } else {
                    echo '0';
                }
                ?>
                </td>
                <?php
            }
            echo "</tr>\n";
        }

        echo '</table>';
    }

    /**
     * creates simple example table with periods as columns
     */
    public function createGridDataHorizontalDates()
    {
        $aPeriods = $this->getGridDataPeriods();
        $aLevels = $this->getGridDataLevels();
        if (empty($aPeriods) || empty($aLevels)) {
            $this->aErrors[] = 'No period or level data provided';

            return false;
        }
        $aAllPeriodLevels = $this->getAllGridDataPeriodLevels($aPeriods, $aLevels);
        ?>
        <table id="dimension_table" border="1" class="dim_table">
        <tbody><tr><th></th>
        <?php
        foreach ($aPeriods as $nPeriodId => $aPeriod) {
            echo "<th>{$aPeriod['period_name_x_axis']}</th>\n";
        }
        ?>
        </tr></tbody>
        <?php
        foreach ($aLevels as $nLevelId => $aLevel) {
            $nLevelId = $aLevel['id'];
            echo "<tr>\n";
            ?>
            <td>
            <?php
            if (!empty($aLevel['link_exists'])) {
            ?>
                <a class="more" title="<?=$aLevel['label']?>" 
               href="?year=<?=$aLevel['nYear']?>&amp;level=<?=$aLevel['level']
               ?>&amp;parent_level_id=<?=$aLevel['level_id']?>"><?=$aLevel['code']?></a>
            <?php
            } else {
                echo $aLevel['code'];
            }
            ?>
            </td>
            <?php
            foreach ($aPeriods as $nPeriodId => $sPeriod) {
                ?>
                <td>
                <?php
                if (!empty($aAllPeriodLevels[$nPeriodId][$nLevelId]['link_exists'])) {
                ?>
                    <a class="more" title="<?=$aLevel['label']?>" 
                   href="?year=<?=$aLevel['nYear']?>&amp;period_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['period_id']?>&amp;level=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['level']?>&amp;parent_level_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['parent_level_id']?>&amp;level_id=<?=
                   $aAllPeriodLevels[$nPeriodId][$nLevelId]['level_id']?>"><?=
                   $this->getFormattedAmt($aAllPeriodLevels[$nPeriodId][$nLevelId]['amt'])?></a>
                <?php
                } else {
                    echo '0';
                }
                ?>
                </td>
                <?php
            }
            echo "</tr>\n";
        }

        echo '</table>';
    }
}
