<?php
/**
 * Monthly period class
 *
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions\Periods;

class Monthly implements IPeriod
{
    const MONTHS_IN_A_YEAR = 12;

    /**
     * set \DateTime object to first day of current month
     * 
     * @param object $date
     */
    public function jumpToPeriodStart(&$date)
    {
        $date->modify('first day of this month');
    }

    /**
     * set \DateTime object to first day of next month
     * 
     * @param object $date
     */
    public function jumpToNextPeriod(&$date)
    {
        $date->modify('first day of next month');
    }

    /**
     * get monthly period array with default values
     * 
     * @return array
     */
    public function getPeriodArray($nYear = false)
    {
        $aPeriods = array();
        for ($n = 1; $n <= self::MONTHS_IN_A_YEAR; $n++) {
            $aPeriods[$n-1] = array(
                'total_amt' => 0,
                'period_name_x_axis' => (strlen($n) == 1 ? "0" : "").$n.".".$nYear
            );
        }

        return $aPeriods;
    }
}
