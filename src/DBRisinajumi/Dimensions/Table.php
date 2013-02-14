<?php
/**
 * Class to get external table info defined in dim_table
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

class Table extends \DBRisinajumi\Dimensions\ADimension
{
    const TRANSACTIONS = 'transactions';
    const LEVELS = 'levels';
    private $aTableTypes = array('transactions', 'levels');

    /**
     * create ne Table instance
     * 
     * @param \mysqli $Database
     */
    public function __construct(\mysqli $Database)
    {
        $this->db = $Database;
    }

    /**
     * get table id from dim_table by table name
     * 
     * @assert ('test_table') == 1
     * @assert ('table_which_does_not_exist') === false
     * @param string $sTableName
     * @return int|boolean
     */
    public function getTableIdByName($sTableName)
    {
        $sSql = "SELECT
            id
        FROM
            dim_table
        WHERE
            table_name = '{$this->db->escape_string($sTableName)}'";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'table ' . $sTableName . ' is not registered in dim_table';

            return false;
        }
        $row = $q->fetch_assoc();

        return $row['id'];
    }

    /**
     * get all tables defined in dim_table
     * 
     * @param string|null $sTableType
     * @return array|boolean
     */
    public function getTables($sTableType = null)
    {
        $sSqlWhere = "";
        if (in_array($sTableType, $this->aTableTypes)) {
            $sSqlWhere = " WHERE table_type = '$sTableType' ";
        }
        $sSql = "SELECT
            id,
            table_type,
            label,
            table_name,
            table_id_field,
            report_select,
            level_select
        FROM
            dim_table
        $sSqlWhere
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'no tables defined in dim_table';

            return false;
        }
        $aTables = array();
        while ($row = $q->fetch_assoc()) {
            $aTables[] = $row;
        }

        return $aTables;
    }

    /**
     * helper method to get additional sql data from table
     * 
     * @param string $sTableName
     * @param string $sTableAlias
     * @return boolean|string
     */
    public function getSqlAdd($sTableName, $sTableAlias = '')
    {
        $aR = array();
        if (empty($sTableAlias)) {
            $sTableAlias = $sTableName;
        }

        $sSql = "SELECT
            *
        FROM
            dim_table
        WHERE
            table_name = '{$this->db->escape_string($sTableName)}'";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'table  '.$sTableName.' is not defined in dim_table';

            return false;
        }
        
        $aRow = $q->fetch_assoc();
        $aR['select'] = "
        ,dim_data.l1_id dim_l1_id
        ,dim_data.l2_id dim_l2_id
        ,dim_data.l3_id  dim_l3_id
        ,dim_data.amt dim_amt
        ,DATE_FORMAT(dim_data.date_from, '{$this->sUserDateFormat}') dim_date_from
        ,DATE_FORMAT(dim_data.date_to, '{$this->sUserDateFormat}') dim_date_to
        ,dim_l1.code dim_l1_code
        ,dim_l2.code dim_l2_code
        ,dim_l3.code dim_l3_code
        ";
        $aR['from'] = "
        LEFT OUTER JOIN  dim_data 
            ON {$this->db->escape_string($sTableAlias)}.".$aRow['table_id_field'].
                " = dim_data.record_id
                AND dim_data.table_id = ".$aRow['id']."
        LEFT OUTER JOIN  dim_l1
            ON dim_data.l1_id = dim_l1.id
        LEFT OUTER JOIN  dim_l2
            ON dim_data.l2_id = dim_l2.id
        LEFT OUTER JOIN  dim_l3
            ON dim_data.l3_id = dim_l3.id
        ";

        return $aR;
    }

    /**
     * validate level, code and label
     * 
     * @todo finish validator
     * @param string $sTableName
     * @param string $sLabel
     * @param string $sTableName
     * @param string $sTableIdField
     * @return boolean
     */
    private function validateFields($sTableName, $sLabel, $sTableType, $sTableIdField)
    {
        $bReturn = true;
        if (empty($sTableName)) {
            $this->aErrors[] = 'table name can not be empty';
            $bReturn = false;
        }
        if (empty($sLabel)) {
            $this->aErrors[] = 'label can not be empty';
            $bReturn = false;
        }

        $sSql = "
        SELECT
            table_name
        FROM
            information_schema.tables
        WHERE
            table_schema = '{$this->db->name}' AND table_name = '{$this->db->escape($sTableName)}';";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'table '.htmlspecialchars().' does not exist';
            $bReturn = false;
        }

        return $bReturn;
    }
    
    /**
     * add table
     * 
     * @param string $sLabel
     * @param string $sTableName
     * @param string $sTableIdField
     * @param string $sSqlReport
     * @param string $sSqlSelect
     * @return int - new table id
     */
    public function addTable($sTableName, $sLabel, $sTableType, $sTableIdField = "id", $sSqlReport = "", $sSqlSelect = "")
    {
        if (!in_array($sTableType, $this->aTableTypes)) {
            $this->aErrors[] =  'wrong table type: '.htmlspecialchars($sTableType);
            
            return false;
        }
        $sSql = "INSERT INTO dim_table
        (
        `label`,
        `table_type`
        `table_name`,
        `table_id_field`,
        `report_select`,
        `level_select`
        ) VALUES (
        '{$this->db->escape($sLabel)}',
        '{$this->db->escape($sLabel)}',
        '{$this->db->escape($sTableName)}',
        '{$this->db->escape($sTableIdField)}',
        '{$this->db->escape($sSqlReport)}',
        '{$this->db->escape($sSqlSelect)}')";
        $this->db->query($sSql) or error_log($this->db->error);

        return $this->db->insert_id;
    }
    
    private function tableIdExists($nTableId)
    {
        $sSql = "
        SELECT
            table_name
        FROM
            dim_table
        WHERE
            id = {$this->db->escape_string($nTableId)}";
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            $this->aErrors[] = 'table id '.$this->db->escape_string($nTableId).' is not registered in dim_table';

            return false;
        }

        return true;
    }
    
    /**
     * update table columns
     * 
     * @param int $nTableId
     * @param string $sTableName
     * @param string $sLabel
     * @param string $sTableIdField
     * @param string $sSqlReport
     * @param string $sSqlSelect
     * @return boolean
     */
    public function updateTable($nTableId, $sTableName, $sLabel, $sTableIdField = "id", $sSqlReport = "", $sSqlSelect = "")
    {
        if (!$this->tableIdExists($nTableId)) {
 
            return false;
        }
        $sSql = "
        UPDATE
            dim_table
        SET
            `label` = '{$this->db->escape($sLabel)}',
            `table_name` = '{$this->db->escape($sTableName)}',
            `table_id_field` = '{$this->db->escape($sTableIdField)}',
            `report_select` = '{$this->db->escape($sSqlReport)}',
            `level_select` = '{$this->db->escape($sSqlSelect)}'
        WHERE
            id = {$this->db->escape($nTableId)}";

        return (boolean)$this->db->query($sSql);
    }
    
    /**
     * delete table record by table id
     * 
     * @param int $nTableId
     * @return boolean
     */
    public function deleteTable($nTableId)
    {
        if (!$this->tableIdExists($nTableId)) {
 
            return false;
        }
        $sSql = "DELETE FROM dim_table WHERE id = $nTableId";

        return (boolean)$this->db->query($sSql);
    }
}
