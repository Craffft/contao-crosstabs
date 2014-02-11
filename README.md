Crosstabs Contao Extension
==========================

Crosstabs is a helper library for developers to realize cross tables in contao

License
-------

This Contao extension is licensed under the terms of the LGPLv3.
http://www.gnu.org/licenses/lgpl-3.0.html

Documentation
-------------

Usage in the config file
```php
// system/modules/mymodule/config/config.php

// Define left table
$GLOBALS['BE_MOD']['accounts']['member'] = array
(
    // Add the cross table to the allowed tables
    'tables' => array('tl_member', 'tl_my_cross_table')
);

// Define right table
$GLOBALS['BE_MOD']['accounts']['mgroup'] = array
(
    // Add the cross table to the allowed tables
    'tables' => array('tl_member_group', 'tl_my_cross_table')
);
```

Usage in the left table
```php
// system/modules/mymodule/dca/tl_member.php

// Copyies the cross table data, if the current record of the left table will be copied
$GLOBALS['TL_DCA']['tl_member']['config']['oncopy_callback'][] = array('\\Crosstabs\\LeftTable', 'copyCallback');
// Deletes the cross table data, if the current record of the left table will be deleted
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = array('\\Crosstabs\\LeftTable', 'deleteCallback');

// ...

// Adds a link icon to the cross table on each left table item
$GLOBALS['TL_DCA']['tl_member']['list']['operations']['my_cross_table_button'] = array
(
    'label'               => &$GLOBALS['TL_LANG']['tl_member']['my_cross_table_button'],
    'href'                => 'table=tl_my_cross_table',
    'icon'                => 'icon.gif'
);

// ...

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
    // The data will be only stored in the cross table and not in this field
    'save_callback'           => array
    (
        array('\\Crosstabs\\Data', 'save')
    ),
    'sql'                     => "blob NULL",
    'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
);
```


Usage in the cross table
```php
// system/modules/mymodule/dca/tl_my_cross_table.php

$GLOBALS['TL_DCA']['tl_my_cross_table'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        // Close the cross table if you only want to read (recommend)
        'closed'                      => true,
        'notEditable'                 => true,
        'notDeletable'                => true,
        'onload_callback'             => array
        (
            array('tl_my_cross_table', 'checkPermission'),
            // Filters the cross table with the given id and the here defined fields
            function (\DataContainer $dc) {
                \Crosstabs\CrossTable::filter($dc, array
                (
                    // If the "do" param is "member", the cross table will be filtered by the given id on the cross table field "member"
                    'member' => 'member',

                    // If the "do" param is "mgroup", the cross table will be filtered by the given id on the cross table field "mgroup"
                    'mgroup' => 'mgroup',

                    // If the "do" param is "do_param", the cross table will be filtered by the given id on the cross table field "cross_table_field"
                    'do_param' => 'cross_table_field',
                ));
            }
        ),

        // ...

        'global_operations' => array
        (
            'left_table_list' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_cross_table']['left_table_list'],
                'href'                => 'do=member&table=tl_member',
                'class'               => 'header_left_table_list',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ),
            'right_table_list' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_cross_table']['right_table_list'],
                'href'                => 'do=mgroup&table=tl_member_group',
                'class'               => 'header_right_table_list',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ),

            // ...
        ),

        // ...

        'operations' => array
        (
            // ...

            // Adds a left table edit icon to the records in the cross table list
            'edit_left_table_item' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_cross_table']['edit_left_table_item'],
                'href'                => 'do=member&amp;table=tl_member&amp;act=edit',
                'icon'                => 'member.gif',
                'button_callback'     => function ($row, $href, $label, $title, $icon) {
                        // Return an icon which opens the edit mode in a popup
                        return \Crosstabs\CrossTable::icon($row['member'], $href, $label, $title, $icon);
                    }
            ),

            // Adds a right table edit icon to the records in the cross table list
            'edit_right_table_item' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_cross_table']['edit_right_table_item'],
                'href'                => 'do=mgroup&amp;table=tl_member_group&amp;act=edit',
                'icon'                => 'mgroup.gif',
                'button_callback'     => function ($row, $href, $label, $title, $icon) {
                        // Return an icon which opens the edit mode in a popup
                        return \Crosstabs\CrossTable::icon($row['mgroup'], $href, $label, $title, $icon);
                    }
            )
        ),

        // ...

        'fields' => array
        (
            // ...

            'member' => array
            (
                'label'                   => &$GLOBALS['TL_LANG']['tl_cross_table']['member'],
                'foreignKey'              => "tl_member.CONCAT(firstname, ' ', lastname)",
                'sql'                     => "int(10) unsigned NOT NULL default '0'",
                'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
            ),
            'mgroup' => array
            (
                'label'                   => &$GLOBALS['TL_LANG']['tl_cross_table']['mgroup'],
                'foreignKey'              => 'tl_member_group.name',
                'sql'                     => "int(10) unsigned NOT NULL default '0'",
                'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
            )
        )

        // ...
    )
);
```