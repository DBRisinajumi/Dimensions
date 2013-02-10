<?php
namespace DBRisinajumi\Dimensions;

/**
 * Test class for Data.
 * Generated by PHPUnit on 2012-11-24 at 19:35:40.
 */
class DataTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Data
     */
    protected $object;
    
    private $nInsertId;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $Database = new \mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASSWORD, TEST_DB);
        $oPeriod = new Period($Database);
        $this->object = new Data($Database, $oPeriod);
        $sSql = "INSERT INTO `dim_table` (`id`, `table_name`) VALUES (1, 'test_table')";
        $this->object->getDbConnection()->query($sSql);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $sSql = "DELETE FROM `dim_table`";
        $this->object->getDbConnection()->query($sSql);
        $sSql = "DELETE FROM `dim_data`";
        $this->object->getDbConnection()->query($sSql);
    }

    /**
     * Generated from @assert (array('')) === false.
     */
    public function testAddRecord() {
        $this->assertSame(
                false, $this->object->addRecord(array(''))
        );
    }

    /**
     * Generated from @assert (array('table_id' => '1', 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-01-01')) === false.
     */
    public function testAddRecord2() {
        $this->assertSame(
                false, $this->object->addRecord(array('table_id' => '1', 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.01.2012'))
        );
    }

    /**
     * Generated from @assert (array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-03-01')) != false.
     */
    public function testAddRecord3() {
        
        $this->assertNotEquals(
                false, $this->object->addRecord(array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.03.2012'))
        );
    }

    /**
     * Generated from @assert (array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '2012-02-01', 'date_to' => '2012-03-01')) === NEW_RECORD_ID.
     */
    public function testUpdateRecord() {
        $nInsertId = $this->object->addRecord(array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.03.2012'));
        $this->assertSame(
                true, $this->object->updateRecord($nInsertId, array('table_id' => 1, 'record_id' => 1, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.04.2012'))
        );
    }
    
    /**
     * check for condition non existing column 
     */
    public function testUpdateRecord2() {
        $nInsertId = $this->object->addRecord(array('table_id' => 1, 'record_id' => 2, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.03.2012'));
        $this->assertFalse($this->object->updateRecord($nInsertId, array('table_id' => 1, 'record_id' => 2, 'l1_id' => 1, 'l2_id' => 2, 'l3_id' => 3, 'amt' => 100, 'date_from' => '01.02.2012', 'date_to' => '01.04.2012', 'non_existing_column' => 'exists'))
        );
    }
    
    public function testGetDimDataId() {
        $nRecordId = 700;
        $aNewRecord = array(
            'table_id' => 1,
            'record_id' => $nRecordId,
            'l1_id' => 1,
            'l2_id' => 2,
            'l3_id' => 3,
            'amt' => 100,
            'date_from' => '01.02.2012',
            'date_to' => '01.03.2012'
        );
        $nDimDataId1 = $this->object->addRecord($aNewRecord);
        $nDimDataId2 = $this->object->getDimDataId('test_table', $nRecordId);
        $this->assertSame($nDimDataId1, $nDimDataId2);
    }

    public function testGetErrors() {
        $this->assertEmpty($this->object->getErrors());
    }
    
    public function testGetErrors2() {
        $this->object->addRecord(array());
        $this->assertNotEmpty($this->object->getErrors());
    }
}

?>