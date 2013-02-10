<?php
/**
 * IPeriod class
 *
 * @author Juris Malinens <juris.malinens@inbox.lv>
 */
namespace DBRisinajumi\Dimensions\Periods;

interface IPeriod
{
    public function jumpToPeriodStart(&$date);
    public function jumpToNextPeriod(&$date);
    public function getPeriodArray($nYear = false);
}
