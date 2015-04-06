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
    'Crosstabs\CrossTable'                      => 'system/modules/crosstabs/library/Crosstabs/CrossTable.php',
    'Crosstabs\Data'                            => 'system/modules/crosstabs/library/Crosstabs/Data.php',
    'Crosstabs\LeftTable'                       => 'system/modules/crosstabs/library/Crosstabs/LeftTable.php',
    'Crosstabs\DataHandler\MultiColumnWizard'   => 'system/modules/crosstabs/library/Crosstabs/DataHandler/MultiColumnWizard.php',
    'Crosstabs\DataHandler\TableLookupWizard'   => 'system/modules/crosstabs/library/Crosstabs/DataHandler/TableLookupWizard.php',
));
