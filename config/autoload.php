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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'Crosstabs',
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Library
    'Crosstabs\CrossData'  => 'system/modules/crosstabs/library/Crosstabs/Data.php',
    'Crosstabs\CrossTable' => 'system/modules/crosstabs/library/Crosstabs/Table.php',
));
