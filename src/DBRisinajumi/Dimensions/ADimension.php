<?php
/**
 * Abstract class for Dimensions
 * 
 * @author Juris Malinens<juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

abstract class ADimension
{
    const MIN_LEVEL = 1;
    const MAX_LEVEL = 3;
    const ROOT_PARENT_LEVEL = 0;
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_3 = 3;

    /**
     * period type
     * @var string 
     */
    protected $sPeriodType = 'monthly';
    protected $aErrors = array();
    protected $db;
    protected $period;
    protected $current_period;
    protected $sUserDateFormat = '%d.%m.%Y';
    protected $sPhpUserDateFormat = 'd.m.Y';

    /**
     * get db connection (for testing purposes)
     * 
     * @return object \mysqli
     */
    public function getDbConnection()
    {
        return $this->db;
    }

    /**
     * set period type (monthly, weekly)
     * 
     * @param string $sPeriodType
     * @throws Exception
     * @uses ADimension::$aAllowedPeriodTypes array of accepted date period types
     */
    public function setPeriodType($sPeriodType)
    {
        $sPeriodClass = '\\DBRisinajumi\\Dimensions\\Periods\\'.ucfirst($sPeriodType);
        if (class_exists($sPeriodClass)) {
            $this->sPeriodType = $sPeriodType;
            $this->current_period = new $sPeriodClass();
        } else {
            $this->aErrors[] = $sPeriodType.' does not exist';
            throw new \InvalidArgumentException;
        }
    }

    /**
     * get array of errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }
}
