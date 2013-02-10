<?php
/**
 * Class for creating database periods
 * or retrieving info of periods from db
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

class Period extends \DBRisinajumi\Dimensions\ADimension
{
    /**
     * create new ReportExample instance
     * 
     * @param \mysqli $Database
     */
    public function __construct(\mysqli $Database)
    {
        $this->db = $Database;
    }

    /**
     * creates single period if we know one date that is in this period
     * 
     * returns
     * <code>
     * array(
     *      'id' => $nLastInsertId,
     *      'in_period_sec' => $row['in_period_sec']
     *      'date_to' => $row['date_to']
     * );
     * </code>
     * @uses Period::jumpToPeriodStart()|Period::jumpToNextPeriod()
     * @param date $dDateTimeFrom
     * @param date $dDateTimeTo
     * @return array
     */
    private function createPeriod($dDateTimeFrom, $dDateTimeTo)
    {
        $date = new \DateTime($dDateTimeFrom);

        $this->current_period->jumpToPeriodStart($date);
        $sPeriodFrom = $date->format("Y-m-d H:i:s");

        $this->current_period->jumpToNextPeriod($date);
        $sPeriodTo = $date->format("Y-m-d H:i:s");
        
        $sSql = "INSERT INTO `dim_period`
        (`period_type`,
        `date_from`,
        `date_to`
        )VALUES(
        '$this->sPeriodType',
        '$sPeriodFrom',
        '$sPeriodTo')";
        $this->db->query($sSql);
        $nLastInsertId = $this->db->insert_id;
        $sSql = "SELECT
          DATE_FORMAT
          (
          CASE
              WHEN STR_TO_DATE('$sPeriodTo', '%Y-%m-%d %H:%i:%s') < STR_TO_DATE('$dDateTimeTo', '%Y-%m-%d %H:%i:%s') 
                THEN STR_TO_DATE('$sPeriodTo', '%Y-%m-%d %H:%i:%s')
              ELSE STR_TO_DATE('$dDateTimeTo', '%Y-%m-%d %H:%i:%s')
          END, '%Y-%m-%d %H:%i:%s'
          ) date_to,
          TIME_TO_SEC(
            TIMEDIFF(
              CASE
                WHEN STR_TO_DATE('$sPeriodTo', '%Y-%m-%d %H:%i:%s') < STR_TO_DATE('$dDateTimeTo', '%Y-%m-%d %H:%i:%s')
                THEN STR_TO_DATE('$sPeriodTo', '%Y-%m-%d %H:%i:%s')
                ELSE STR_TO_DATE('$dDateTimeTo', '%Y-%m-%d %H:%i:%s')
              END,
              STR_TO_DATE('$dDateTimeFrom', '%Y-%m-%d %H:%i:%s')
            )
          )  in_period_sec
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'this should not happen!';

            return false;
        }
        $row = $q->fetch_assoc();
        
        return array(
            'id' => $nLastInsertId,
            'date_to' => $row['date_to'],
            'in_period_sec' => $row['in_period_sec']
        );
    }

    /**
     * get first period_id, new date_from and seconds in this period
     * 
     * return format:
     * <code>
     * array(
     *     'id' => N,
     *     'date_to' => DATE,
     *     'in_period_sec' => N
     * );
     * </code>
     * @todo returned date_to must be in format [DATE] HH:MM:SS
     * @param date $dDateTimeFrom
     * @param date $dDateTimeTo
     * @return boolean|array
     */
    public function getPeriodLengthData($dDateTimeFrom, $dDateTimeTo)
    {
        $sSql = "SET @date_from = STR_TO_DATE('{$dDateTimeFrom}', '%Y-%m-%d %H:%i:%s');";
        $this->db->query($sSql);

        $sSql = "SET @date_to = STR_TO_DATE('{$dDateTimeTo}', '%Y-%m-%d %H:%i:%s');";
        $this->db->query($sSql);

        $sSql = "
        SELECT
          id,
          DATE_FORMAT
          (
          CASE
              WHEN date_to < @date_to 
                THEN date_to 
              ELSE @date_to 
          END, '%Y-%m-%d %H:%i:%s'
          ) date_to,
          TIME_TO_SEC(
            TIMEDIFF(
              CASE
                WHEN date_to < @date_to
                THEN date_to
                ELSE @date_to
              END,
              @date_from
            )
          )  in_period_sec
        FROM
          dim_period
        WHERE period_type = '$this->sPeriodType'
          AND date_from <= @date_from
          AND date_to > @date_from
        ORDER BY date_from
        LIMIT 1
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {

            return $this->createPeriod($dDateTimeFrom, $dDateTimeTo);
        }

        return $q->fetch_assoc();
    }
}
