<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2014 Daniel Kiesel
 *
 * @package Crosstabs
 * @link    https://github.com/craffft/contao-crosstabs
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace Crosstabs;

/**
 * Class LeftTable
 *
 * @copyright  Daniel Kiesel 2014
 * @author     Daniel Kiesel <daniel@craffft.de>
 */
class LeftTable extends \Controller
{

    /**
     * Copies the cross table items from the current copied left table item
     *
     * @access public
     * @static
     * @param  int $intPasteId
     * @param  \DataContainer $dc
     */
    public static function copyCallback($intPasteId, \DataContainer $dc)
    {
        $intCopyId = $dc->id;

        if (isset($GLOBALS['TL_DCA'][$dc->table]['fields'])) {
            foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'] as $k => $v) {
                if (!isset($v['crossTable']) || !isset($v['crossCurrentKey'])) {
                    continue;
                }

                // Get model name
                $strModel = \Model::getClassFromTable($v['crossTable']);

                // Check if model exists
                if (!class_exists($strModel)) {
                    return;
                }

                // Get model object
                $objModel = $strModel::findBy($v['crossCurrentKey'], $intCopyId);

                if ($objModel !== null) {
                    while ($objModel->next()) {
                        $objCopy = clone $objModel->current();
                        $objCopy->$v['crossCurrentKey'] = $intPasteId;
                        $objCopy->save();
                    }
                }
            }
        }
    }

    /**
     * Removes the cross table items from the current deleted left table item
     *
     * @access public
     * @static
     * @param  \DataContainer $dc
     */
    public static function deleteCallback(\DataContainer $dc)
    {
        if (!$dc->activeRecord) {
            return;
        }

        $intId = $dc->activeRecord->id;
        $strTable = $dc->table;

        if (isset($GLOBALS['TL_DCA'][$dc->table]['fields'])) {
            foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'] as $k => $v) {
                if (!isset($v['crossTable']) || !isset($v['crossCurrentKey'])) {
                    continue;
                }

                // Get model name
                $strModel = \Model::getClassFromTable($v['crossTable']);

                // Check if model exists
                if (!class_exists($strModel)) {
                    return;
                }

                // Get model object
                $objModel = $strModel::findBy($v['crossCurrentKey'], $intId);

                if ($objModel !== null) {
                    // Get tl_undo data
                    $objUndo = \Database::getInstance()->prepare("SELECT * FROM tl_undo WHERE fromTable=? ORDER BY id DESC")->limit(1)->execute($dc->table);
                    $arrSet = $objUndo->row();

                    // Deserialize tl_undo data
                    $arrSet['data'] = deserialize($arrSet['data']);

                    while ($objModel->next()) {
                        // Add cross table record to undo data
                        $arrSet['data'][$v['crossTable']][] = $objModel->row();

                        // Delete cross table item
                        $objModel->delete();
                    }

                    // Serialize tl_undo data
                    $arrSet['data'] = serialize($arrSet['data']);

                    // Update tl_undo
                    \Database::getInstance()->prepare("UPDATE tl_undo %s WHERE id=?")->set($arrSet)->execute($objUndo->id);
                }
            }
        }
    }
}
