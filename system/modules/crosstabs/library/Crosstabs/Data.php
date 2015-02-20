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
 * Class Data
 *
 * @copyright  Daniel Kiesel 2014
 * @author     Daniel Kiesel <daniel@craffft.de>
 */
class Data extends \Controller
{

    /**
     * Loading data via cross table eg. for a select box
     *
     * @access public
     * @static
     * @param  string         $varValue
     * @param  \DataContainer $dc
     * @return string
     */
    public static function load($varValue, \DataContainer $dc)
    {
        // Get dca field
        $dca = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field];

        // Get model
        $strModel = \Model::getClassFromTable($dca['crossTable']);

        // Get object
        $objItem = $strModel::findBy($dca['crossCurrentKey'], $dc->id);

        if ($objItem !== null) {
            // Add the cid values to an serialized array
            return serialize(array_values($objItem->fetchEach($dca['crossForeignKey'])));
        }

        return '';
    }

    /**
     * Saving data via cross table eg. for a select box
     *
     * @access public
     * @static
     * @param  string         $varValue
     * @param  \DataContainer $dc
     * @return string
     */
    public static function save($varValue, \DataContainer $dc)
    {
        $arrItems = deserialize($varValue);

        // Check if is array
        if (!is_array($arrItems)) {
            $arrItems = array();
        }

        // Get dca field
        $dca = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field];

        // Get model
        $strModel = \Model::getClassFromTable($dca['crossTable']);

        // Delete old cross items
        self::removeOldItems($strModel, $arrItems, $dc->id, $dca['crossCurrentKey'], $dca['crossForeignKey']);

        // Add new cross items
        self::addNewItems($strModel, $arrItems, $dc->id, $dca['crossCurrentKey'], $dca['crossForeignKey']);

        return '';
    }

    /**
     * Removing old items when saving data in a cross table
     *
     * @access protected
     * @static
     * @param  string $strModel
     * @param  array  $arrItems
     * @param  int    $intId
     * @param  string $cck
     * @param  string $cfk
     * @return void
     */
    protected static function removeOldItems($strModel, array $arrItems, $intId, $cck, $cfk)
    {
        // If some items are selected
        if (count($arrItems) > 0) {
            $t = $strModel::getTable();
            $objItem = $strModel::findBy(array("$t.$cck=? AND $t.$cfk NOT IN(" . implode(',', array_map('intval', $arrItems)) . ")"), array($intId));
        }
        // If no items are selected
        else {
            $objItem = $strModel::findBy($cck, $intId);
        }

        if ($objItem !== null) {
            while ($objItem->next()) {
                // Delete old items
                $objItem->delete();
            }
        }
    }

    /**
     * Adding new items when saving data in a cross table
     *
     * @access protected
     * @static
     * @param  string $strModel
     * @param  array  $arrItems
     * @param  int    $intId
     * @param  string $cck
     * @param  string $cfk
     * @return void
     */
    protected static function addNewItems($strModel, array $arrItems, $intId, $cck, $cfk)
    {
        // Check if is array and has items
        if (count($arrItems) > 0) {
            $t = $strModel::getTable();

            foreach ($arrItems as $intFk) {
                // Get object
                $objItem = $strModel::findBy(array("$t.$cck=? AND $t.$cfk=?"), array($intId, $intFk));

                // If object not exists
                if ($objItem === null) {
                    // Create new object and save it
                    $objItem = new $strModel();
                    $objItem->tstamp = time();
                    $objItem->$cck  = $intId;
                    $objItem->$cfk = $intFk;

                    $objItem->save();
                }
            }
        }
    }
}
