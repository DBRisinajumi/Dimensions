<?php
/**
 * Generates Reports from dimension data
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

class Report extends \DBRisinajumi\Dimensions\ADimension
{
    /**
     * current level id
     * @var int 
     */
    private $nLevel = self::MIN_LEVEL;
    /**
     * dimension id
     * @var int
     */
    private $nDimId;
    /**
     * parent level id if it exists
     * @var int|null 
     */
    private $nParentLevel = self::ROOT_PARENT_LEVEL;
    /**
     * current active year
     * @var int
     */
    private $nYear;

    /**
     * create new Report instance
     * 
     * @param \mysqli $Database
     */
    public function __construct(\mysqli $Database)
    {
        $this->db = $Database;
        $this->nYear = date('Y'); //default to current year
    }
    
    /**
     * get formatted amount of money
     * 
     * @assert (0) == '0.00'
     * @param int $nAmt
     * @return string
     */
    public function getFormattedAmt($nAmt)
    {
        //money_format() ?
        return number_format($nAmt/100, 2, '.', ' ');
    }
    
    /**
     * get all used years in ascending order
     * 
     * @return boolean|array
     */
    public function getYears()
    {
        $sSql = "
        SELECT 
            YEAR(date_from) AS `year`
        FROM
            dim_data
        UNION
        SELECT 
            YEAR(date_to) AS `year`
        FROM
            dim_data
        GROUP BY `year`
        ORDER BY YEAR ASC";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'no years detected';

            return false;
        }
        $aYears = array();
        while ($row = $q->fetch_assoc()) {
            $aYears[] = $row;
        }

        return $aYears;
    }

    // @codeCoverageIgnoreStart

    // @codeCoverageIgnoreEnd
    /**
     * set four digit year
     * 
     * @param int $nYear
     */
    public function setYear($nYear)
    {
        if (is_numeric($nYear) && $nYear >= 2011) {
            $this->nYear = $nYear;
        } else {
            $this->aErrors[] = 'year must be newer than 2011';
            $this->nYear = date('Y');
        }
    }

    /**
     * get current active year
     * 
     * @return int
     */
    public function getYear()
    {
        return $this->nYear;
    }

    /**
     * 
     * @param type $nYear
     * @return string
     */
    public function changeYearInUri($nYear)
    {
        $nYear = (int)$nYear;
        $aGet = $_GET;
        $aGet['year'] = $nYear;

        return http_build_query($aGet, '', '&amp;');
    }
    /**
     * get year before active current year if such year exists
     * 
     * @return int|boolean
     */
    public function getPrevYear()
    {
        $aFilterYears = array_reverse($this->getYears());
        if (!empty($aFilterYears)) {
            foreach ($aFilterYears as $aYear) {
                if ($aYear['year'] < $this->nYear) {
                    return $aYear['year'];
                }
            }
        }

        return false;
    }

    /**
     * get year after active current year if such year exists
     * 
     * @return int|boolean
     */
    public function getNextYear()
    {
        $aFilterYears = $this->getYears();
        if (!empty($aFilterYears)) {
            foreach ($aFilterYears as $aYear) {
                if ($aYear['year'] > $this->nYear) {
                    return $aYear['year'];
                }
            }
        }

        return false;
    }
    // @codeCoverageIgnoreStart
    /**
     * set Level and dimension id (level_id)
     * 
     * @param int $nLevel
     * @param int $nLevelId
     */
    public function setLevel($nLevel = 1, $nLevelId = null)
    {
        if (empty($nLevelId) || !is_numeric($nLevelId)) {
            $this->aErrors[] = 'dimension id must be numeric and non-empty value';
        }
        $this->nLevel = (int)$nLevel;
        $this->nParentLevel = $nLevel-1;
        $this->nDimId = (int)$nLevelId;
    }
    // @codeCoverageIgnoreEnd
    /**
     * get level data
     * 
     * @return boolean|int
     */
    public function getGridDataLevels()
    {
        if ($this->nLevel > self::MIN_LEVEL) {
            $sParentSql = 'l'.$this->nParentLevel.'_id';
        } else {
            $sParentSql = '1';
        }
        $sSql = "SELECT 
             id,
             $sParentSql as parent_level_id,
             code,
             label
        FROM
              dim_l".$this->nLevel;
        if ($this->nParentLevel > self::ROOT_PARENT_LEVEL) {
            $sSql .= " WHERE l".$this->nParentLevel."_id = ".$this->nDimId;
        }
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {

            return false;
        }
        $aLevels = array();
        while ($row = $q->fetch_assoc()) {
            if ($this->nParentLevel < self::MAX_LEVEL-1) {
                $row['link_exists'] = true;
                $row['level'] = $this->nLevel+1;
                $row['level_id'] = $row['id'];
            }
            $row['nYear'] = $this->nYear;
            $row['total_amt'] = 0;
            $aLevels[] = $row;
        }

        return $aLevels;
    }

    /**
     * get periods for grid/table
     * 
     * @todo make simpler. no need for DB?
     * @return boolean|int
     */
    public function getGridDataPeriods()
    {
        $aPeriods = $this->current_period->getPeriodArray($this->nYear);
        $sSql = "
        SELECT
            dim_period.id,
            (CASE WHEN (dim_period.`period_type` = 'monthly')
             THEN DATE_FORMAT(dim_period.`date_from`,'%m.%Y')
             ELSE DATE_FORMAT(dim_period.`date_from`,'%U') 
             END
             ) AS period_name_x_axis
        FROM
            dim_l{$this->nLevel}
        LEFT OUTER JOIN dim_data_period ON
            (dim_l{$this->nLevel}.`id` = dim_data_period.`l{$this->nLevel}_id`)
        LEFT OUTER JOIN dim_period ON
            (dim_data_period.`period_id` = dim_period.`id`)
        WHERE dim_period.`period_type` = '{$this->sPeriodType}' 
            AND date_from >= '".($this->nYear)."-01-01'
            AND date_to <= '".($this->nYear+1)."-01-01'
        GROUP BY id
        "; //%U week nr 00...53
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {

            return false;
        }
        while ($row = $q->fetch_assoc()) {
            foreach ($aPeriods as $nId => $aPeriod) {
                if ($aPeriod['period_name_x_axis'] == $row['period_name_x_axis']) {
                    /**
                     * fill default 0 for total amount for current period.
                     * later fill value in getAllGridDataPeriodLevels
                     */
                    $row['total_amt'] = 0;
                    $aPeriods[$nId] = $row;
                }
            }
        }

        return $aPeriods;
    }

    /**
     * combine levels with periods
     * 
     * @param array $aPeriods
     * @param array $aLevels
     * @return boolean|int
     */
    public function getAllGridDataPeriodLevels(&$aPeriods, $aLevels)
    {
        $aPeriodIds = array();
        if (empty($aPeriods)) {
            $this->aErrors[] = 'no period data provided for getAllGridDataPeriodLevels';

            return false;
        }
        foreach ($aPeriods as $aPeriod) {
            if (isset($aPeriod['id'])) { //period with data
                $aPeriodIds[] = $aPeriod['id'];
            }
        }
        $aLevelIds = array();
        if (empty($aLevels)) {
            $this->aErrors[] = 'no level data provided for getAllGridDataPeriodLevels';

            return false;
        }
        foreach ($aLevels as $aLevel) {
            $aLevelIds[] = $aLevel['id'];
        }
        $sSqlLevelIds = implode(', ', $aLevelIds);
        $sSqlPeriodIds = implode(', ', $aPeriodIds);
        $sSql = "
        SELECT 
          SUM(period_amt) AS amt,
          l{$this->nLevel}_id,
          period_id
        FROM
          dim_data_period 
        WHERE period_id IN ( $sSqlPeriodIds ) 
          AND l{$this->nLevel}_id IN ( $sSqlLevelIds )
        GROUP BY period_id, l{$this->nLevel}_id
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'no period level data provided for getAllGridDataPeriodLevels';

            return false;
        }
        $aAllPeriodLevels = array();
        while ($row = $q->fetch_assoc()) {
            //find period index where period_id match
            foreach ($aPeriods as $nId => $aPeriod) {
                if (!isset($aPeriod['id']) || $aPeriod['id'] != $row['period_id']) {
                    continue;
                }
                $row['link_exists'] = true;
                $row['nYear'] = $this->nYear;
                $row['level'] = $this->nLevel;
                $row['level_id'] = $row['l'.$this->nLevel.'_id'];
                $row['parent_level_id'] = $this->nDimId;
                $aAllPeriodLevels[$nId][$row['l'.$this->nLevel.'_id']] = $row;
                /**
                 * add to total sum for current period
                 */
                $aPeriods[$nId]['total_amt'] += $row['amt'];
            }
        }

        foreach (array_keys($aPeriods) as $nPeriodId) {
            foreach ($aLevels as $aLevel) {
                $nLevelId = $aLevel['id'];
                if (!isset($aAllPeriodLevels[$nPeriodId][$nLevelId]['link2'])) {
                    $aAllPeriodLevels[$nPeriodId][$nLevelId]['html'] = 0;
                }
            }
        }
        ksort($aAllPeriodLevels); //fix month order for vlib

        return $aAllPeriodLevels;
    }

    /**
     * get dim_data records for specific grid cell from which amount is made of
     * 
     * @param int $nPeriodId
     * @param int $nLevel
     * @param int $nLevelId
     * @return array|boolean
     */
    public function createListData($nPeriodId, $nLevel, $nLevelId)
    {
        $nPeriodId = (int)$nPeriodId;
        $nLevel = (int)$nLevel;
        $nLevelId = (int)$nLevelId;
        $sSql = "
        SELECT 
          dim_data_period.`period_id`,
          dim_data_period.`period_amt`,
          dim_data.`amt`,
          dim_data.`table_id`,
          dim_table.`table_name`,
          dim_data.`record_id`,
          dim_table.report_select
        FROM
          dim_data_period 
          LEFT JOIN dim_data ON
          (dim_data_period.`dim_data_id` = dim_data.`id`)
          LEFT JOIN dim_table ON
          (dim_data.`table_id` = dim_table.`id`)
        WHERE dim_data_period.period_id = $nPeriodId
          AND dim_data_period.l{$nLevel}_id = $nLevelId
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {

            return false;
        }
        $aReturn = array();
        while ($row = $q->fetch_assoc()) {

            $row['amt'] = $this->getFormattedAmt($row['amt']);
            $row['period_amt'] = $this->getFormattedAmt($row['period_amt']);
            // get table data
            $sSql = 'set @record_id = ' . $row['record_id'];
            $this->db->query($sSql);
            if (!isset($row['report_select'])) {
                //print_r($row);
            }
            $qTable = $this->db->query($row['report_select']);
            if ($qTable->num_rows > 0) {
                $row = $row + $qTable->fetch_array();
            }

            $aReturn[] = $row;
        }

        return $aReturn;
    }
}
