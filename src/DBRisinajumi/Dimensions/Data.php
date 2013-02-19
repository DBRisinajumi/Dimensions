<?php
/**
 * Class for manipulating amount data into periods proportionally
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

class Data extends \DBRisinajumi\Dimensions\ADimension
{
    const DB_DATE_FORMAT = '%Y-%m-%d %H:%i:%s';
    const PHP_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * create new Data isntance
     * 
     * @param \mysqli $Database
     * @param \DBRisinajumi\Dimension\Period $oPeriod
     */
    public function __construct(\mysqli $Database, Period $oPeriod)
    {
        $this->db = $Database;
        $this->period = $oPeriod;
    }

    /**
     * proportionally split amount in periods with second precision
     * 
     * <code>
     * Example 100 USD from 2012.01.14 to 2012.03.5:
     * 2012.01
     * 	        days	amount
     * 2012.01	18	36
     * 2012.02	28	56
     * 2012.03	4	8
     * Total	50	100
     * </code>
     * 
     * <code>
     * array(
     *     array(period_id=>n,period_amt=>m),
     *     array(period_id=>n1,period_amt=>m1)
     * 
     * …..,
     * )
     * </code>
     * checks if end date is bigger or equal than start date
     * 
     * @assert (100, '2012-03-14', '2012-01-04') === false
     * @assert (100, '2012-01-14', '2012-03-5') == array('')
     * @uses Data::getPeriodLengthData()
     * @todo make it work with weekly periods
     * @param int $nAmt - amount to split (if in money then in cents)
     * @param date $dDateTimeFrom
     * @param date $dDateTimeTo
     * @return array $aPeriods
     */
    private function splitAmtInPeriods($nAmt, $dDateTimeFrom, $dDateTimeTo)
    {
        /**
         * iterate to next period while helper date is still older or the same as end date
         */
        $aPeriods = array();
        $nTotalPeriodAmt = 0;
        $nPeriodSeconds = '';

        /**
         * split in periods
         */
        while ($aPeriodData = $this->period->getPeriodLengthData($dDateTimeFrom, $dDateTimeTo)) {
            $nPeriodSeconds += $aPeriodData['in_period_sec'];

            $aPeriods[] = array(
                'period_id' => $aPeriodData['id'],
                'in_period_sec' => $aPeriodData['in_period_sec']
            );

            $dDateTimeFrom = $aPeriodData['date_to'];
            if ($dDateTimeFrom == $dDateTimeTo) {
                break;
            }
        }

        /**
         * calc amt for each period
         */
        foreach ($aPeriods as $kP => $aPeriod) {
            if ($nPeriodSeconds == 0) {
                // start date and end date equal
                $nK = 1;
            } else {
                $nK = $aPeriod['in_period_sec']/$nPeriodSeconds;
            }
            $nPeriodAmt = floor($nAmt * $nK);
            $aPeriods[$kP]['period_amt'] = $nPeriodAmt;
            $nTotalPeriodAmt += $nPeriodAmt;
        }

        /**
         * fix amt
         */
        if ($nAmt != $nTotalPeriodAmt && count($aPeriods) > 0) {
            $nLastPeriodKey = count($aPeriods) - 1;
            $aPeriods[$nLastPeriodKey]['period_amt'] += $nAmt - $nTotalPeriodAmt;
        }

        return $aPeriods;
    }

    /**
     * get dim_data if we know table id and record id
     * 
     * @param int $nTableId
     * @param int $nRecordId
     * @return boolean|int
     */
    public function getDimData($nTableId, $nRecordId)
    {
        $sSql = "
            SELECT
              dim_data.id,
              `table_id`,
              `record_id`,
              dim_data.l1_id,
              dim_data.l2_id,
              dim_data.l3_id,
              `amt`,
              `date_from`,
              `date_to`,
              dim_l1.code l1_code,
              dim_l2.code l2_code,
              dim_l3.code l3_code
            FROM
              dim_data 
              LEFT OUTER JOIN dim_l1
                ON dim_data.l1_id = dim_l1.id
              LEFT OUTER JOIN dim_l2
                ON dim_data.l2_id = dim_l2.id
              LEFT OUTER JOIN dim_l3
                ON dim_data.l3_id = dim_l3.id
            WHERE
                dim_data.record_id = {$this->db->escape_string($nRecordId)} AND
                dim_data.table_id = {$this->db->escape_string($nTableId)}
        ";
        $q = $this->db->query($sSql) or error_log($this->db->error);
        if ($q->num_rows == 0) {

            return false;
        }

        return $q->fetch_assoc();
    }

    /**
     * get date format in which user enters dates
     * @param string $sFormatType - php or mysql
     * @return string
     */
    public function getUserDateFormat($sFormatType = 'php')
    {
        if ($sFormatType == 'php') {
            return  str_replace('%', '', $this->sUserDateFormat);
        }
        return $this->sUserDateFormat; //mysql
    }
    
    /**
     * converts user input date to internal/univeral Y-m-d H:i:s format
     * 
     * @param string $dDate
     * @return string
     */
    private function toInternalDate($dDate)
    {
        $date = \DateTime::createFromFormat($this->getUserDateFormat(), $dDate);

        return $date->format(self::PHP_DATE_FORMAT);
    }
    
    /**
     * adds new record in table dim_data
     * 
     * <ul>
     * <li>returns false if all required data are not present</li>
     * <li>runs generateDataPeriods() method, to make sure are periods are generated</li>
     * <li>runs splitAmtInPeriods method at the end</li>
     * <li>using date_from un date_to un saves results dim_data_period</li>
     * </ul>
     * all required data not present
     * @assert (array('')) === false
     * end date newer than start date
     * @assert (array('table_id' => '1', 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-01-01')) === false
     * correct params
     * @assert (array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-03-01')) === NEW_RECORD_ID
     * @param array $aData
     * @return int $nDimDataId
     */
    public function addRecord($aData)
    {
        if ($this->validateFields($aData) === false) {

            return false;
        }
        $aData['date_from'] = $this->toInternalDate($aData['date_from']);
        $aData['date_to'] = $this->toInternalDate($aData['date_to']);

        $sSql = "INSERT INTO `dim_data`
        (`table_id`, `record_id`, `l1_id`, `l2_id`, `l3_id`, `amt`, `date_from`, `date_to`)
        VALUES
        (
        '{$this->db->escape_string($aData['table_id'])}',
        '{$this->db->escape_string($aData['record_id'])}', 
        '{$this->db->escape_string($aData['l1_id'])}', 
        '{$this->db->escape_string($aData['l2_id'])}',
        '{$this->db->escape_string($aData['l3_id'])}',
        '{$this->db->escape_string($aData['amt'])}', 
        STR_TO_DATE('{$this->db->escape_string($aData['date_from'])}','".self::DB_DATE_FORMAT."'),
        STR_TO_DATE('{$this->db->escape_string($aData['date_to'])}','".self::DB_DATE_FORMAT."')
        )";
        $this->db->query($sSql);
        $nDimDataId = $this->db->insert_id;
        $this->generateDataPeriods($nDimDataId, $aData);

        return $nDimDataId;
    }

    /**
     * updates record in dim_data
     * 
     * <ul>
     * <li>returns false if asll required data is not present</li>
     * <li>deletes all current dim_data record's dim_data_period records
     * and regenerates them with createRecords() method</li>
     * <li>splits amount proportionally by periods with splitAmtInPeriods() method</li>
     * <li>saves  updated data back to dim_data_period</li>
     * </ul>
     * @assert (array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-03-01')) === true
     * @uses Data::validate_fields()|Data::toInternalDate()|Data::generateDataPeriods()
     * @param int $nDimDataId
     * @param array $aData
     * @return boolean
     */
    public function updateRecord($nDimDataId, $aData)
    {
        if ($this->validateFields($aData) === false) {

            return false;
        }

        $aData['date_from'] = $this->toInternalDate($aData['date_from']);
        $aData['date_to'] = $this->toInternalDate($aData['date_to']);

        $nDimDataId = (int)$nDimDataId;
        $aUpdateSql = array();
        foreach ($aData as $sColumnName => $sColumnValue) {
            $sColumnValue = $this->db->escape_string($sColumnValue);
            if ($sColumnName == 'date_from' || $sColumnName == 'date_to') {
                $aUpdateSql[] = " `$sColumnName` = STR_TO_DATE('$sColumnValue', '".self::DB_DATE_FORMAT."') ";
                continue;
            }
            $aUpdateSql[] = " `$sColumnName` = '$sColumnValue' ";
        }
        $sSql = "UPDATE `dim_data` SET ".implode(', ', $aUpdateSql)." WHERE id = $nDimDataId";
        //echo $sSql;
        if (!$this->db->query($sSql)) {
            $this->aErrors[] = 'update dimension_data row with id ' . $nDimDataId . ' failed' .
            isset($this->db->error) ? '. Error: ' . $this->db->error : '';

            return false;
        }
        $sSql = "DELETE FROM `dim_data_period` WHERE `dim_data_id` = $nDimDataId";
        $this->db->query($sSql);
        $this->generateDataPeriods($nDimDataId, $aData);

        return true;
    }

    /**
     * generatas and later saves dim_data_period records
     * 
     * @uses Data::splitAmtInPeriods()
     * @param int $nDimDataId
     * @param array $aData
     * @return boolean
     */
    private function generateDataPeriods($nDimDataId, $aData)
    {
        $aAmt = $this->splitAmtInPeriods($aData['amt'], $aData['date_from'], $aData['date_to']);
        foreach ($aAmt as $aPeriod) {
            $sSql = "INSERT INTO `dim_data_period`
            (`dim_data_id`, `period_id`, `period_amt`, `l1_id`, `l2_id`, `l3_id`)
            VALUES
            ('$nDimDataId',
            '{$aPeriod['period_id']}',
            '{$aPeriod['period_amt']}',
            '{$this->db->escape_string($aData['l1_id'])}',
            '{$this->db->escape_string($aData['l2_id'])}',
            '{$this->db->escape_string($aData['l3_id'])}')";
            $this->db->query($sSql);
        }

        return true;
    }

    /**
     * checks if addRecord() and updateRecord() methods have all necessary fields
     * 
     * @param array $aData
     * @return boolean
     */
    private function validateFields($aData)
    {
        $aRequiredFields = array(
            'table_id', 'record_id', 'l1_id', 'l2_id', 'l3_id', 'amt', 'date_from', 'date_to'
        );
        foreach ($aRequiredFields as $sField) {
            if (empty($aData[$sField])) {
                $this->aErrors[] = 'field ' . $sField . ' is required to add new record';

                return false;
            }
        }

        $aNumericFields = array('table_id', 'record_id', 'l1_id', 'l2_id', 'l3_id', 'amt');
        foreach ($aNumericFields as $sField) {
            if (!is_numeric($aData[$sField]) || $aData[$sField] <= 0) {
                $this->aErrors[] = 'field ' . $sField . ' must be numeric and positive value';

                return false;
            }
        }

        $aData['date_from'] = $this->toInternalDate($aData['date_from']);
        $aData['date_to'] = $this->toInternalDate($aData['date_to']);
        if (strtotime($aData['date_from']) > strtotime($aData['date_to'])) {
            $this->aErrors[] = 'date from: '.$aData['date_from'].' can not be bigger than date to '.$aData['date_to'];

            return false;
        }

        return true;
    }
}
