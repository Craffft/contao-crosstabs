<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2014 Daniel Kiesel
 *
 * @package Crosstabs
 * @link    https://github.com/icodr8/contao-crosstabs
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
 * @author     Daniel Kiesel <https://github.com/icodr8>
 */
class LeftTable extends \Controller
{

    /**
     * Copies the cross table items from the current copied left table item
     *
     * @access public
     * @static
     * @param  \DataContainer $dc
     */
    public static function copyCallback(\DataContainer $dc)
    {
        // Todo
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
        // Todo
    }
}
