<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;
use lindesbs\minkorrekt\Constants\BearbeitungsStatus;

$GLOBALS['TL_DCA']['tl_minkorrekt_folgen'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ctable' => ['tl_minkorrekt_folgen_inhalt'],
        'switchToEdit' => true,
        'markAsCopy' => 'title',
        'onsubmit_callback' => [],

        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index'
            ],
        ],
    ],
    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTABLE,
            'panelLayout' => 'filter;search,limit,sort',
        ],
        'label' => [
            'fields' => ['wip', 'episode', 'title'],
            'format' => '%s :: %s :: %s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ]
        ],
        'operations' => [
            'edit',
            'children',
            'delete',
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,published,episode,wip,isIGNobel',
    ],
    'fields' => [
        'id' => ['label' => ['ID'], 'sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => ''],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'published' => ['toggle' => true, 'filter' => true, 'inputType' => 'checkbox', 'eval' => ['doNotCopy' => true], 'sql' => ['type' => 'boolean', 'default' => false]],
        'alias' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,
            'eval' => [
                'rgxp' => 'alias',
                'doNotCopy' => true,
                'maxlength' => 255,
                'readonly' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'episode' => [
            'exclude' => true,
            'inputType' => 'text',
            'sorting' => true,
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
        ],
        'content' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'search' => true,
            'eval' => ['decodeEntities' => true],
            'sql' => "text NULL'",
        ],
        'pubdate' => [
            'exclude' => true,
            'inputType' => 'text',
            'sorting' => true,
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
        ],
        'duration' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
        ],
        'wip' => [
            'label' => &$GLOBALS['TL_LANG']['tl_news']['minkorrekt_wip'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'radio',
            'options' => [
                BearbeitungsStatus::UNBEARBEITET,
                BearbeitungsStatus::IN_BEARBEITEUNG,
                BearbeitungsStatus::ABGENOMMEN,

            ],
            'eval' => ['tl_class' => 'w50'],
            'sql' => sprintf("varchar(16) NOT NULL default '%s'", BearbeitungsStatus::UNBEARBEITET)
        ],
        'isIGNobel' => ['toggle' => true, 'filter' => true, 'inputType' => 'checkbox', 'eval' => ['doNotCopy' => true], 'sql' => ['type' => 'boolean', 'default' => false]],


    ],
];
