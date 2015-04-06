<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2014-2015 Daniel Kiesel
 *
 * @package Crosstabs
 * @link    https://github.com/craffft/contao-crosstabs
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace Crosstabs\DataHandler;

/**
 * Class TableLookupWizard
 *
 * @copyright  Daniel Kiesel 2014-2015
 * @author     Daniel Kiesel <daniel@craffft.de>
 */
class TableLookupWizard extends \Controller
{
    /**
     * @param $varValue
     * @param \DataContainer $dc
     * @return string
     */
    public static function load($varValue, \DataContainer $dc)
    {
        $dca = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field];
        $strModel = \Model::getClassFromTable($dca['crossTable']);
        $objItem = $strModel::findBy($dca['crossCurrentKey'], $dc->id);

        if ($objItem !== null) {
            return serialize(array_values($objItem->fetchEach($dca['crossForeignKey'])));
        }

        return '';
    }

    /**
     * @param $varValue
     * @param \DataContainer $dc
     * @return string
     */
    public static function save($varValue, \DataContainer $dc)
    {
        $arrItems = deserialize($varValue);

        if (!is_array($arrItems)) {
            $arrItems = array();
        }

        $dca = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field];
        $strModel = \Model::getClassFromTable($dca['crossTable']);

        self::removeOldItems($strModel, $arrItems, $dc->id, $dca['crossCurrentKey'], $dca['crossForeignKey']);
        self::addNewItems($strModel, $arrItems, $dc->id, $dca['crossCurrentKey'], $dca['crossForeignKey']);

        return '';
    }

    /**
     * @param $strModel
     * @param array $arrItems
     * @param $intId
     * @param $strCrossCurrentKey
     * @param $strCrossForeignKey
     */
    protected static function removeOldItems($strModel, array $arrItems, $intId, $strCrossCurrentKey, $strCrossForeignKey)
    {
        if (count($arrItems) > 0) {
            $t = $strModel::getTable();
            $objItem = $strModel::findBy(array("$t.$strCrossCurrentKey=? AND $t.$strCrossForeignKey NOT IN(" . implode(',', array_map('intval', $arrItems)) . ")"), array($intId));
        } else {
            $objItem = $strModel::findBy($strCrossCurrentKey, $intId);
        }

        if ($objItem !== null) {
            while ($objItem->next()) {
                $objItem->delete();
            }
        }
    }

    /**
     * @param $strModel
     * @param array $arrItems
     * @param $intId
     * @param $strCrossCurrentKey
     * @param $strCrossForeignKey
     */
    protected static function addNewItems($strModel, array $arrItems, $intId, $strCrossCurrentKey, $strCrossForeignKey)
    {
        if (count($arrItems) > 0) {
            $t = $strModel::getTable();

            foreach ($arrItems as $intFk) {
                $objItem = $strModel::findBy(array("$t.$strCrossCurrentKey=? AND $t.$strCrossForeignKey=?"), array($intId, $intFk));

                if ($objItem === null) {
                    $objItem = new $strModel();
                    $objItem->tstamp = time();
                    $objItem->$strCrossCurrentKey  = $intId;
                    $objItem->$strCrossForeignKey = $intFk;
                }

                $objItem->save();
            }
        }
    }
}
