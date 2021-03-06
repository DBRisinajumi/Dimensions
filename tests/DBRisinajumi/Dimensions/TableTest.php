<?php
namespace DBRisinajumi\Dimensions;

/**
 * Test class for Table.
 * Generated by PHPUnit on 2013-02-02 at 16:51:46.
 */
class TableTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Table
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $Database = new \mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASSWORD, TEST_DB);
        $this->object = new Table($Database);
        $sSql = "INSERT INTO `dim_table` (`id`, `table_name`) VALUES (1, 'test_table')";
        $this->object->getDbConnection()->query($sSql);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * Generated from @assert ('test_table') == 1.
     */
    public function testGetTableIdByName() {
        $this->assertEquals(
                1, $this->object->getTableIdByName('test_table')
        );
    }

    /**
     * Generated from @assert ('table_which_does_not_exist') === false.
     */
    public function testGetTableIdByName2() {
        $this->assertSame(
                false, $this->object->getTableIdByName('table_which_does_not_exist')
        );
    }

    /**
     * @todo Implement testGetTables().
     */
    public function testGetTables() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * Non existing table
     */
    public function testGetSqlAdd() {
        $this->assertFalse($this->object->getSqlAdd('non_existing_table'));
    }
    
    public function testGetSqlAdd2() {
        $this->assertArrayHasKey('select', $this->object->getSqlAdd('test_table'));
    }

}

?>
