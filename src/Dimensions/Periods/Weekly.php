<?php
/**
 * Weekly period class
 *
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions\Periods;

class Weekly implements IPeriod
{
    const WEEKS_IN_A_YEAR = 53;

    /**
     * set \DateTime object to first day of current week
     * 
     * @param object $date
     */
    public function jumpToPeriodStart(&$date)
    {
        $date->modify('this monday');
    }

    /**
     * set \DateTime object to first day of next week
     * 
     * @param object $date
     */
    public function jumpToNextPeriod(&$date)
    {
        $date->modify('next monday');
    }

    /**
     * get weekly period array with default values
     * 
     * @return array
     */
    public function getPeriodArray($nYear = false)
    {
        $aPeriods = array();
        for ($n = 0; $n <= self::WEEKS_IN_A_YEAR; $n++) {
            $aPeriods[$n] = array(
                'total_amt' => 0,
                'period_name_x_axis' => strlen($n) == 1 ? "0".$n : $n
            );
        }

        return $aPeriods;
    }
}
