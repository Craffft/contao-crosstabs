Crosstabs Contao Extension
==========================

Crosstabs is a helper library for developers to realize cross tables in contao

License
-------

This Contao extension is licensed under the terms of the LGPLv3.
http://www.gnu.org/licenses/lgpl-3.0.html

Documentation
-------------

Usage in the left table
```php
// system/modules/mymodule/dca/tl_member.php

// Copyies the cross table data, if the current record of the left table will be copied
$GLOBALS['TL_DCA']['tl_member']['config']['oncopy_callback'][] = array('\\Crosstabs\\LeftTable', 'copyCallback');
// Deletes the cross table data, if the current record of the left table will be deleted
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = array('\\Crosstabs\\LeftTable', 'deleteCallback');

// Adds an icon link to the records in the list
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['my_cross_table_button'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['tl_member']['my_cross_table_button'],
    'href'                => 'table=tl_my_cross_table',
    'icon'                => 'icon.gif'
);

// Adds a field to select the related items from the right table
$GLOBALS['TL_DCA']['tl_member']['fields']['groups'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['groups'],
    'exclude'                 => true,
    'filter'                  => true,
    // This example uses the tablelookupwizard from terminal42
    'inputType'               => 'tableLookup',
    'foreignKey'              => 'tl_member_group.name',
    // Name of the cross table
    'crossTable'              => 'tl_my_cross_table',
    // Key from the left table
    'crossCurrentKey'         => 'member',
    // Key from the right table
    'crossForeignKey'         => 'mgroup',
    'eval'                    => array
    (
        'tl_class'            => 'clr',
        'foreignTable'        => 'tl_member_group',
        'fieldType'           => 'checkbox',
        'listFields'          => array('name'),
        'searchFields'        => array('name'),
        'matchAllKeywords'    => true
    ),
    // Loads the data from the cross table
    'load_callback'           => array
    (
        array('\\Crosstabs\\Data', 'load')
    ),
    // Saves the data into the cross table and truncates the data in the current field
    // The data will be only stored in the cross table and not this field
    'save_callback'           => array
    (
        array('\\Crosstabs\\Data', 'save')
    ),
    'sql'                     => "blob NULL",
    'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
);
```
