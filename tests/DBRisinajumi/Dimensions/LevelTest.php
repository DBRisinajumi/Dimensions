<?php
namespace DBRisinajumi\Dimensions;

/**
 * Test class for Level.
 * Generated by PHPUnit on 2012-11-19 at 00:00:11.
 */
class LevelTest extends \PHPUnit_Framework_TestCase {

    private $nId;
    private $nListLevelId;
    /**
     * @var Level
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $Database = new \mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASSWORD, TEST_DB);
        $this->object = new Level($Database);
        $this->nId = $this->object->addLevel(null, 1, 'code', 'label', null);
        $this->nListLevelId = $this->object->addLevel(999, 2, 'code', 'label', null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object->getDbConnection()->query('DELETE FROM dim_l1');
        $this->object->getDbConnection()->query('DELETE FROM dim_l2');
        $this->object->getDbConnection()->query('DELETE FROM dim_l3');
    }

    /**
     * Generated from @assert (1, 1, 'code', '') === false.
     */
    public function testEmptyLabelAddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(1, 1, 'code', '', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (1, 1, '', 'label') === false.
     */
    public function testEmptyCodeAddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(1, 1, '', 'label', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (1, 0, 'code', 'label') === false.
     */
    public function testLevelLessThan1AddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(1, 0, 'code', 'label', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (1, 4, 'code', 'label') === false.
     */
    public function testLevelBiggerThan3AddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(1, 4, 'code', 'label', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (null, 2, 'code', 'label') === false.
     */
    public function testRequiredParentNotProvidedAddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(null, 2, 'code', 'label', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (null, 3, 'code', 'label') === false.
     */
    public function testRequiredParentNotProvidedAddLevel2() {
        $this->assertSame(
                false
                , $this->object->addLevel(null, 3, 'code', 'label', Level::VISIBLE, 999)
        );
    }

    /**
     * Generated from @assert (1, 1, 'code', 'label') === false.
     */
    public function testParentNotEmptyForLevel1AddLevel() {
        $this->assertSame(
                false
                , $this->object->addLevel(1, 1, 'code', 'label', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (1, 2, 'code', 'label') > 0.
     */
    public function testCorrectValuesAddLevel() {
        $nId = $this->object->addLevel(1, 2, 'code', 'label', Level::VISIBLE, null);
        //print_r($this->object->getErrors());
        $this->assertGreaterThan(0, $nId);
    }
    
    /**
     * @dataProvider addLevelProvider
     */
    public function testCorrectValuesFromDataproviderAddLevel($nParrentLevelId, $nLevel, $sCode, $sLabel, $nLevelsTableId) {
        $this->assertGreaterThan(
                0
                , $this->object->addLevel($nParrentLevelId, $nLevel, $sCode, $sLabel, $nLevelsTableId)
        );
    }

    /**
     * Generated from @assert (1, '', 'newlabel', 0) === false.
     */
    public function testUpdateLevel() {
        $this->assertSame(
                false
                , $this->object->updateLevel(Level::LEVEL_1, $this->nId, '', 'newlabel', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (null, 1, 'newcode', '', 0) === false.
     */
    public function testUpdateLevel2() {
        $this->assertSame(
                false
                , $this->object->updateLevel(Level::LEVEL_1, $this->nId, 'newcode', '', Level::VISIBLE, null)
        );
    }

    /**
     * Generated from @assert (null, 1, 'newcode', 'newlabel', 0) === true.
     */
    public function testUpdateLevel3() {
        $this->assertSame(
                true
                , $this->object->updateLevel(Level::LEVEL_1, $this->nId, 'newcode', 'newlabel', Level::HIDDEN, null)
        );
    }
    
    /**
     * non existent dimension id
     */
    public function testUpdateLevel4() {
        $this->assertSame(
                false
                , $this->object->updateLevel(Level::LEVEL_1, 999999, 'newcode', 'newlabel', Level::HIDDEN)
        );
    }

    /**
     * Generated from @assert (1, 1) === true.
     */
    public function testHideLevel() {
        $this->assertSame(
                true
                , $this->object->setLevel($this->nId, Level::LEVEL_1, Level::HIDDEN)
        );
    }
    
    public function testSetLevelBad() {
        $this->assertSame(
                false
                , $this->object->setLevel($this->nId, Level::LEVEL_1, 'bad_hidden_value')
        );
    }

    /**
     * Generated from @assert (1) === true.
     */
    public function testShowLevel() {
        $this->assertSame(
                true
                , $this->object->setLevel($this->nId, Level::LEVEL_1, Level::VISIBLE)
        );
    }
    
    public function testGetLevelItem() {
        $aAssumedResult = array(
            'id' => $this->nId,
            'dim_id' => $this->nId,
            'label' => 'label',
            'code' => 'code',
            'hidden' => Level::VISIBLE,
            'level' => Level::LEVEL_1
        );
        $this->assertEquals(
                $aAssumedResult
                , $this->object->getLevelItem(Level::LEVEL_1, $this->nId)
        );
    }

    /**
     * tearDown metodē tiek dzēsts pievienotais ieraksts
     */
    public function testListLevel() {
        
        $aAssumedResult = array(
                    0 => array(
                        'id' => $this->nListLevelId,
                        'label' => 'label',
                        'value' => 'label',
                        'parent_id' => 999,
                        'level' => Level::LEVEL_2,
                        'code' => 'code',
                        'hidden' => Level::VISIBLE,
                        'external' => false
                    )
                );
        //print_r($aAssumedResult);
        $aResult = $this->object->listLevel(999, 2, 'cod');
        //print_r($aResult);
        $this->assertEquals($aAssumedResult, $aResult);
    }
    
    public function addLevelProvider() {
        return array(
          array(null, 1, 'testcode1', 'testlabel1', null),
          array(1, 2, 'testcode2', 'testlabel2', null)
        );
    }
}
