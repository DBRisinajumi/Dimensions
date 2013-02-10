<?php
/**
 * ajax helper for dimension tree menu administration
 * 
 * @author Juris Malinens <juris.malinens@inbox.lv>
 * @author Uldis Nelsons <uldisnelsons@gmail.com>
 */
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
use \DBRisinajumi\Dimension\Level;
use \DBRisinajumi\Dimension\Table;
use \DBRisinajumi\Dimension\Data;
use \DBRisinajumi\Dimension\Period;

$sAction = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'get_tree';
$nLevel = isset($_REQUEST['level']) ? (int)$_REQUEST['level'] : 1;
$nParrentLevelId = isset($_REQUEST['parent_level_id']) ? (int)$_REQUEST['parent_level_id'] : 0;

require 'init.php';

$nId = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$aDirectJson = array();

$oLevel = new Level($Database);
$oTable = new Table($Database);

switch ($sAction) {

    case 'get_selectbox_values':
        $aLevel = $oLevel->listLevel($nParrentLevelId, $nLevel);
        if ($nLevel < 3) {
            $sNextLevel = '_' . ($nLevel + 1);
        } else {
            $sNextLevel = '';
        }
        foreach ($aLevel as $aLevelRecord) {
            $aData = array('title' => $aLevelRecord['code'], 'id' => $aLevelRecord['id']);
            if ($aLevelRecord['hidden']) {
                continue;
            }
            $a = array(
                'data' => $aData,
                "attr" => array(
                'id' => $aLevelRecord['id'],
                )
            );
            if (!empty($sNextLevel)) {
                $a['state'] = 'closed';
            }

            $aDirectJson[] = $a;
        }
        break;
    case 'get_tree':
        if ($nId == '0') {
            $nLevel = 1;
            $nParrentLevelId = '';
        } else {
            $a = explode('_', $nId);
            if (count($a) == 2) {
                $nLevel = $a[1];
                $nParrentLevelId = $a[0];
            }
        }
        if (!empty($nLevel)) {
            $aLevel = $oLevel->listLevel($nParrentLevelId, $nLevel, true);
            if ($nLevel < 3) {
                $sNextLevel = '_' . ($nLevel + 1);
            } else {
                $sNextLevel = '';
            }
            foreach ($aLevel as $aLevelRecord) {
                $aData = array('title' => $aLevelRecord['label']);
                if ($aLevelRecord['external']) {
                    $aData['icon'] = 'images/ticket.png';
                }elseif ($aLevelRecord['hidden']) {
                    $aData['icon'] = 'images/folder--exclamation.png';
                } else {
                    $aData['icon'] = 'images/folder.png';
                }
                $a = array(
                    'data' => $aData,
                    );
                if (!$aLevelRecord['external']) {
                    $a['attr'] =   array(
                        'id' => $aLevelRecord['id'] . $sNextLevel,
                        );
                }
                if (!empty($sNextLevel)) {
                    $a['state'] = 'closed';
                }


                $aDirectJson[] = $a;
            }
        }

        break;
    case'get_form':
        $a = explode('_', $nId);
        if (count($a) != 2) {
            $nLevel = 3;
            $nLevelId = $nId;
        } else {
            $nLevel = $a[1] - 1;
            $nLevelId = $a[0];
        }

        $aLevel = $oLevel->getLevelItem($nLevel, $nLevelId);
        $aTables = $oTable->getTables();
        if ($aLevel['level'] != $oLevel::LEVEL_3) {
            $show_add_form = true;
        }
        ob_start();
        require 'views/form.php';
        $aDirectJson['html'] = ob_get_clean();
        break;

    case'save_dim_data':
        $sTranTableName = $_REQUEST['table_name'];
        $nRecordId = (int)$_REQUEST['record_id'];
        $nL1Id = (int)$_REQUEST['l1_id'];
        $nL2Id = (int)$_REQUEST['l2_id'];
        $nL3Id = (int)$_REQUEST['l3_id'];
        //amt convert to cents
        $nAmt = (int)$_REQUEST['amt'] * 100;
        $dDateFrom = $_REQUEST['date_from'];
        $dDateTo = $_REQUEST['date_to'];

        $oPeriod = new Period($Database);
        $oData = new Data($Database, $oPeriod);
        $oData->setPeriodType('monthly');
        //get table_id
        $nTableId = $oTable->getTableIdByName($sTranTableName);

        //get existing dimension data record
        $nDimDataId = $oData->getDimDataId($sTranTableName, $nRecordId);

        //fix level3 if it has external table
        $nL3Id = $oLevel->fixLevelId($oLevel::LEVEL_3, $nL2Id, $nL3Id);

        $aData = array(
            'table_id' => $nTableId,
            'record_id' => $nRecordId,
            'l1_id' => $nL1Id,
            'l2_id' => $nL2Id,
            'l3_id' => $nL3Id,
            'amt' => $nAmt,
            'date_from' => $dDateFrom,
            'date_to' => $dDateTo,
        );
        if ($nDimDataId === false) {
            if (!$oData->addRecord($aData)) {
                $aDirectJson['error'] = implode(PHP_EOL, $oData->getErrors());
            }
        } else {
            if (!$oData->updateRecord($nDimDataId, $aData)) {
                $aDirectJson['error'] = implode(PHP_EOL, $oData->getErrors());
            }
        }
        break;

    case'save_form':
        $nLevel = (int)$_REQUEST['level'];
        $sCode = $_REQUEST['code'];
        $sLabel = $_REQUEST['label'];
        $nHidden = isset($_REQUEST['hidden']) ? (int)$_REQUEST['hidden'] : Level::VISIBLE;
        $nExternalTableId = isset($_REQUEST['table_id']) ? (int)$_REQUEST['table_id'] : null;
        if (!$oLevel->updateLevel($nLevel, $nId, $sCode, $sLabel, $nHidden, $nExternalTableId)) {
            $aDirectJson['error'] = implode(PHP_EOL, $oLevel->getErrors());
        }

        if (!empty($nExternalTableId)) {

            $nExternalLevelId = $oLevel->getExternalLevelId($nId);
            if ($nExternalLevelId) {
                $oLevel->updateLevel(Level::LEVEL_3, $nExternalLevelId, 'test', 'test', Level::VISIBLE, $nExternalTableId);
            } else {
                $oLevel->addLevel($nId, Level::LEVEL_3, 'test', 'test', $nExternalTableId);
            }
        }
        break;

    case'delete_level':
        $nLevel = (int)$_REQUEST['level'];
        $sCode = $_REQUEST['code'];
        $sLabel = $_REQUEST['label'];
        $nHidden = isset($_REQUEST['hidden']) ? (int)$_REQUEST['hidden'] : Level::VISIBLE;
        if (!$oLevel->deleteLevelItem($nLevel, $nId)) {
            $aDirectJson['error'] = implode(PHP_EOL, $oLevel->getErrors());
        }
        break;

    case'save_add_form':
        $nLevel = (int)$_REQUEST['level'] + 1;
        $sCode = $_REQUEST['code'];
        $sLabel = $_REQUEST['label'];
        $nHidden = isset($_REQUEST['hidden']) ? (int)$_REQUEST['hidden'] : Level::VISIBLE;
        $nExternalTableId = isset($_REQUEST['table_id']) ? (int)$_REQUEST['table_id'] : null;
        $nParrentLevelId = $nId;
        $nNewLevelId = $oLevel->addLevel($nParrentLevelId, $nLevel, $sCode, $sLabel, $nHidden, $nExternalTableId);
        if (!$nNewLevelId) {
            $aDirectJson['error'] = implode(PHP_EOL, $oLevel->getErrors());
        }

        /**
         * fix inserting 3rd level with external table 
         * when form is submitted not from 3rd level in which case 
         * default addLevel() works fine
         */
        if (
            !empty($nExternalTableId) &&
            $nLevel < Level::LEVEL_3 &&
            !empty($nNewLevelId)
        ) {
            //$oLevel->addExternalTableLevel($nLevel, $nExternalTableId, $nNewLevelId);
            $nExternalTableLevelId = $oLevel->addLevel($nNewLevelId, Level::LEVEL_3, 'test', 'test', Level::VISIBLE, $nExternalTableId);
            if (!$nExternalTableLevelId) {
                $aDirectJson['error'] = implode(PHP_EOL, $oLevel->getErrors());
            }
        }

    default:
        break;
}

//ajax response in json
echo json_encode($aDirectJson);
