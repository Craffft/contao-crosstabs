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
 * Class CrossTable
 *
 * @copyright  Daniel Kiesel 2014
 * @author     Daniel Kiesel <daniel@craffft.de>
 */
class CrossTable extends \Controller
{

    /**
     * filter function.
     *
     * @access public
     * @static
     * @param  \DataContainer $dc
     * @param  array          $arrFields
     * @return void
     */
    public static function filter(\DataContainer $dc, array $arrFields)
    {
        $do = \Input::get('do');
        $intId = \Input::get('id');

        if (is_array($arrFields) && isset($arrFields[$do]) && is_numeric($intId) && $intId > 0) {
            $GLOBALS['TL_DCA'][$dc->table]['list']['sorting']['filter'][] = array($arrFields[$do] . '=?', $intId);
        }
    }

    /**
     * icon function.
     *
     * @access public
     * @static
     * @param  int    $intId
     * @param  string $href
     * @param  string $label
     * @param  string $title
     * @param  string $icon
     * @return string
     */
    public static function icon($intId, $href, $label, $title, $icon)
    {
        return '<a href="' . \Backend::addToUrl($href . '&amp;id=' . $intId . '&amp;popup=1') . '" title="' . specialchars($title) . '" onclick="Backend.openModalIframe({\'width\':765,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($title, $intId))) . '\',\'url\':this.href});return false">' . \Image::getHtml($icon, $label) . '</a> ';
    }
}
