<?php
/**
 * Class for manipulatings level tree
 * 
 * @author Juris Malinens<juris.malinens@inbox.lv>
 * @author Uldis Nelsons<uldisnelsons@gmail.com>
 */
namespace DBRisinajumi\Dimensions;

class Level extends \DBRisinajumi\Dimensions\ADimension
{
    const HIDDEN = 1;
    const VISIBLE = 0;
    const AJAX_RESULT_LIMIT = 10;

    /**
     * create new Level instance
     * 
     * @param \mysqli $Database
     */
    public function __construct(\mysqli $Database)
    {
        $this->db = $Database;
    }

    /**
     * validate level, code and label
     * 
     * @param int $nLevel
     * @param string $sCode
     * @param string $sLabel
     * @param int $nHidden
     * @param int|null $nLevelsTableId
     * @return boolean
     */
    private function validateFields($nLevel, $sCode, $sLabel, $nHidden, $nLevelsTableId)
    {
        $bReturn = true;
        if (empty($sCode)) {
            $this->aErrors[] = 'code can not be empty';
            $bReturn = false;
        }
        if (empty($sLabel)) {
            $this->aErrors[] = 'label can not be empty';
            $bReturn = false;
        }
        if ($nLevel < self::MIN_LEVEL || $nLevel > self::MAX_LEVEL) {
            $this->aErrors[] = 'Level(' . $nLevel . ') should be between 1 and 3';
            $bReturn = false;
        }

        if (!in_array($nHidden, array(self::HIDDEN, self::VISIBLE))) {
            $this->aErrors[] = 'Incorect HIDDEN field value: ' . $nHidden;
            $bReturn = false;
        }

        if ($nLevelsTableId !== null && !is_numeric($nLevelsTableId)) {
            $this->aErrors[] = 'External table id '.(string)$nLevelsTableId.' must be null or numeric ';
            $bReturn = false;
        }

        return $bReturn;
    }

    /**
     * does level 2 level has defined level 3 level with external table?
     * if yes, return level id
     * 
     * @param int $nLevelId
     * @return boolean|int
     */
    public function getExternalLevelId($nLevelId)
    {
        $nLevelId = (int)$nLevelId;
        $sSql = "
        SELECT 
          `id` 
        FROM
          `dim_l3` 
        WHERE l2_id = $nLevelId 
          AND levels_table_id IS NOT NULL 
        ";
        $q = $this->db->query($sSql);
        if ($q->num_rows > 0) {
            $row = $q->fetch_assoc();
            return $row['id'];
        }

        return false;
    }

    /**
     * add dimension level to DB
     * 
     * dim_id is the same as id
     * dim_id created for potential use where we can save external data
     * code field is required
     * @assert (1, 1, 'code', '') === false
     * label field is required
     * @assert (1, 1, '', 'label') === false
     * dimension levels can only be 1, 2, 3
     * @assert (1, 0, 'code', 'label') === false
     * @assert (1, 4, 'code', 'label') === false
     * $nLevel can only be 1, if we don't have $nParrentLevelId
     * @assert (null, 2, 'code', 'label') === false
     * @assert (null, 3, 'code', 'label') === false
     * if we have $nParrentLevelId, then $nLevel can only be 2, 3
     * @assert (1, 1, 'code', 'label') === false
     * if all params ar good we retrieve new record id
     * @assert (1, 2, 'code', 'label') > 0
     * @param int $nParrentLevelId
     * @param int $nLevel
     * @param string $sCode
     * @param string $sLabel
     * @param int $nHidden
     * @param int|null $nLevelsTableId
     * @return boolean|int - boolean false if error otherwise new record id
     */
    public function addLevel($nParrentLevelId, $nLevel, $sCode, $sLabel, $nHidden, $nLevelsTableId = null)
    {
        if ($this->validateFields($nLevel, $sCode, $sLabel, $nHidden, $nLevelsTableId) === false) {

            return false;
        }
        if ($nParrentLevelId === null && $nLevel > self::MIN_LEVEL) {
            $this->aErrors[] = 'If parent level is NULL it means level value can be only '.
            self::MIN_LEVEL;

            return false;
        }
        if ($nParrentLevelId > 0 && $nLevel == self::MIN_LEVEL) {
            $this->aErrors[] = 'If there is parent level it means level value can be '
            .(self::MIN_LEVEL + 1) . ' and not bigger than ' . self::MAX_LEVEL;

            return false;
        }

        $sColumnName = $sColumnValue = $sColumnName2 = $sColumnValue2 = '';
        if ($nParrentLevelId !== null) {
            $nParrentLevelId = (int)$nParrentLevelId;
            //column name for one level higher
            $sColumnName = ', `l' . ($nLevel - 1) . '_id` ';
            $sColumnValue = ", '" . $nParrentLevelId . "'\n";
        }
        if (!empty($nLevelsTableId) && $nLevel == self::LEVEL_3) {
            $nLevelsTableId = (int)$nLevelsTableId;
            $sColumnName2 = ', `levels_table_id` ';
            $sColumnValue2 = ", $nLevelsTableId \n";
        }
        $sSql = "INSERT INTO `dim_l$nLevel` 
                 (
                    `code`,
                    `label`,
                    `hidden`
                     $sColumnName
                     $sColumnName2
                  ) VALUES (
                     '{$this->db->escape_string($sCode)}',
                     '{$this->db->escape_string($sLabel)}',
                     '0'
                     $sColumnValue
                     $sColumnValue2
                  )
                ";
        $this->db->query($sSql);// or die($this->db->error);
        //update dim_id value from insert id
        $nInsertedId = $this->db->insert_id;
        $sSql = "UPDATE `dim_l$nLevel` SET dim_id = $nInsertedId where id = $nInsertedId";
        $this->db->query($sSql);

        return $nInsertedId;
    }

    /**
     * change label and field values for dimension
     * 
     * code field is required
     * @assert (null, 1, '', 'newlabel', 0) === false
     * label field is required
     * @assert (null, 1, 'newcode', '', 0) === false
     * OK if all params are passed correctly
     * @assert (null, 1, 'newcode', 'newlabel', 0) === true
     * @param int $nLevel - level so we no which table to use
     * @param int $nLevelId - table id
     * @param string $sCode
     * @param string $sLabel
     * @param int $nHidden
     * @param int|null $nLevelsTableId
     * @return boolean
     */
    public function updateLevel($nLevel, $nLevelId, $sCode, $sLabel, $nHidden, $nLevelsTableId = null)
    {
        if ($this->validateFields($nLevel, $sCode, $sLabel, $nHidden, $nLevelsTableId) === false) {

            return false;
        }
        if (!$this->getLevelItem($nLevel, $nLevelId)) {

            return false;
        }
        $sSql = "UPDATE
            `dim_l$nLevel`
        SET
            code = '{$this->db->escape_string($sCode)}',
            label = '{$this->db->escape_string($sLabel)}',
            hidden = '$nHidden'
        WHERE
            id = {$this->db->escape_string($nLevelId)}
        ";
        //echo $sSql;
        $this->db->query($sSql) or error_log($this->db->error);

        //delete level 3 external table if in level 2 there is no table selected
        if ($nLevel == self::LEVEL_2 && empty($nLevelsTableId)) {
            $sSql = "
            DELETE FROM dim_l3
            WHERE
                l2_id = {$this->db->escape_string($nLevelId)}
            LIMIT 1
            ";
            $this->db->query($sSql) or error_log($this->db->error);
        }

        return true;
    }

    /**
     * enable/disable showing specific dimension level
     * 
     * @param int $nLevelId
     * @param int $nLevel
     * @param int $nHidden - self::HIDDEN or self::VISIBLE
     * @return boolean
     */
    public function setLevel($nLevelId, $nLevel, $nHidden = self::VISIBLE)
    {
        $nLevelId = (int)$nLevelId;
        $sSql = "UPDATE
            `dim_l$nLevel`
        SET
            hidden = {$this->db->escape_string($nHidden)}
        WHERE
            id = $nLevelId
        ";
        $this->db->query($sSql);
        $sSql = "SELECT hidden FROM `dim_l$nLevel` WHERE id = $nLevelId";
        $q = $this->db->query($sSql);
        $row = $q->fetch_assoc();
        if ($row['hidden'] == $nHidden) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * get dimension list for tree component or listbox
     * 
     * @param int $nParrentLevelId - prev DIM id, ignored if root dimension
     * @param int $nLevel - for which level return data
     * @param string $bShowHidden - show hidden elements, true use for tree components
     */
    public function listLevel($nParrentLevelId, $nLevel, $bShowHidden = false)
    {
        $nLevel = (int)$nLevel;
        $nParrentLevelId = (int)$nParrentLevelId;
        $sParrentLevelField = 'l' . ($nLevel - 1);
        $sSql = "";
        $bExternal = false;

        if ($nLevel == 3) {
            //is external table for level?
            $sSql .= "
                SELECT
                    dim_table.level_select
                FROM
                    dim_l3 dl
                LEFT OUTER JOIN dim_table
                    ON dl.levels_table_id = dim_table.id
                WHERE
                    dl.l2_id = $nParrentLevelId
                    AND NOT dl.levels_table_id is null
                ";
            $q = $this->db->query($sSql);
            if ($q->num_rows == 1) {

                //get select from dim_table
                $aRow = $q->fetch_assoc();
                $sSql = $aRow['level_select'];
                $bExternal = true;
            } else {
                $sSql = '';
            }
        }
        if (empty($sSql)) {

            if ($nLevel == 1) {
                $sWhere = "";
            } else {
                $sWhere = "
                WHERE
                    {$sParrentLevelField}_id = $nParrentLevelId
                ";
            }
            $sSql .= "
            SELECT
                *
            FROM
                dim_l$nLevel
            $sWhere
            ORDER BY
                code
            ";
        }

        //echo $sSql;
        $q = $this->db->query($sSql);

        $aReturn = array();
        while ($row = $q->fetch_assoc()) {

            if (!$bShowHidden && $row['hidden'] ) {
                //don't show hidden
                continue;
            }
            
            $a = array(
                'id' => $row['id'],
                'label' => $row['label'],
                'value' => $row['label'],
                'code' => $row['code'],
                'hidden' => $row['hidden'],
                'level' => $nLevel,
                'external' => $bExternal,
            );
            if ($sParrentLevelField != 'l0') {
                $a['parent_id'] = $nParrentLevelId;
            }
            $aReturn[] = $a;
        }

        return $aReturn;
    }

    /**
     * get level item info by level id and dimension id
     * 
     * @param int $nLevel
     * @param int $nId
     * @return array
     */
    public function getLevelItem($nLevel, $nId)
    {
        $nLevel = (int)$nLevel;
        $nId = (int)$nId;
        $sSql = "
            SELECT
                *
            FROM
                dim_l$nLevel
            WHERE
                id = " . $nId;
        //echo $sSql;
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            return false;
        }
        $aReturn = $q->fetch_assoc();
        $aReturn['level'] = $nLevel;
        
        if ($nLevel == self::LEVEL_2) {
            $sSql = "
                SELECT
                    id,
                    levels_table_id
                FROM
                    dim_l3
                WHERE
                    l2_id = " . $aReturn['id'];
            //echo $sSql;
            $q = $this->db->query($sSql);
            $aLevel3 = $q->fetch_assoc();
            $aReturn['levels_table_id'] = $aLevel3['levels_table_id'];
            $aReturn['external_table_id_level'] = $aLevel3['id'];
        }

        return $aReturn;
    }

    /**
     * delete level item by level id and dimension id
     * 
     * @param int $nLevel
     * @param int $nId
     * @return boolean
     */
    public function deleteLevelItem($nLevel, $nId)
    {
        /**
         * check for sublevel
         */
        if ($nLevel < self::LEVEL_3) {
            $aSubLevelItem = $this->listLevel($nId, $nLevel+1, true);
            if (count($aSubLevelItem) > 0) {
                $this->aErrors[] = 'Can not delete level. It has childrens ';

                return false;
            }
        }

        /**
         * check if used in dim_data
         */
        $sSql = "SELECT COUNT(*) count FROM dim_data WHERE l".$nLevel."_id = ".$nId;
        $q = $this->db->query($sSql);
        $aData = $q->fetch_assoc();
        if ($aData['count'] > 0) {
            $this->aErrors[] = 'Can not delete level. It used in dim_data ';

            return false;
        }
        $nLevel = (int) $nLevel;
        $sSql = "
            DELETE
            FROM
                dim_l$nLevel
            WHERE
                id = ".$nId;

        //echo $sSql;
        $this->db->query($sSql);

        return true;
    }

    /**
     * if selected level is from external table, level_id is rom this table
     * function search real level_id or add it from table
     * currently external tables can use only in level 3
     * @param int $nLevel
     * @param int $nParrentLevelId
     * @param int $nLevelId
     * @return int dim_l*.id
     */
    public function fixLevelId($nLevel, $nParrentLevelId, $nLevelId)
    {
        //currently only in level 3 we can use external tables
        if ($nLevel != self::LEVEL_3) {

            return $nLevelId;
        }
        $nLevel = (int)$nLevel;
        $nParrentLevelId = (int)$nParrentLevelId;
        $nLevelId = (int)$nLevelId;
        // is level3 external table?
        $sSql = "
        SELECT
          dim_table.level_select
        FROM
          dim_l3
          INNER JOIN dim_table
            ON levels_table_id = dim_table.id
        WHERE l2_id = '$nParrentLevelId'
          AND `code` IS NULL
        ";
        //var_dump($sSql);
        $q = $this->db->query($sSql);
        if ($q->num_rows == 0) {
            //no external table for level

            return $nLevelId;
        }

        //get external table sql select
        $row = $q->fetch_array();
        $sTableSelect = $row['level_select'];

        //is record defined in dim_l3?
        $sSql = "
        SELECT
          *
        FROM
          dim_l3
        WHERE
          l2_id = '$nParrentLevelId'
          AND dim_id = '$nLevelId'
          AND NOT `code` IS NULL
        ";
        //echo $sSql;
        $q = $this->db->query($sSql);
        if ($q->num_rows == 1) {
            //get corect id
            $row = $q->fetch_assoc();

            return $row['id'];
        }
        //get code and label
        //var_dump($sTableSelect);
        $q = $this->db->query($sTableSelect);
        while ($aExtTableRow = $q->fetch_array()) {
            if ($aExtTableRow['id'] == $nLevelId) {
                break;
            }
        }

        //add external table record to level3
        $sSql = "INSERT INTO dim_l3 (l2_id, dim_id, `code`, label)
        VALUES
        ('$nParrentLevelId', '$nLevelId', '".$aExtTableRow['code']."', '".$aExtTableRow['label']."');";

        $this->db->query($sSql);
        return $this->db->insert_id;
    }
}
