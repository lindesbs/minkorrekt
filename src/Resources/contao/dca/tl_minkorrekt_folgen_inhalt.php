<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;
use lindesbs\minkorrekt\Constants\BearbeitungsStatus;
use lindesbs\minkorrekt\Constants\ThemenArt;

$GLOBALS['TL_DCA']['tl_minkorrekt_folgen_inhalt'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_minkorrekt_folgen',
        'switchToEdit' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,published' => 'index'
            ]
        ]
    ],

    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['sorting'],
            'headerFields' => ['title','published','wip'],
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['thema_art','thema_art','text'],
            'format' => '%s<div class="be_minkorrekt_list_%s">%s</div>',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations' => [
            'edit',
            'delete',
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'showInHeader' => true
            ]
        ]
    ],

    'palettes' => [
        '__selector__' => [],
        'default' => '{title_legend},thema_art,thema_nr,text;published;link',
    ],
    'subpalettes' => [],

    // Fields
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid' => ['foreignKey' => 'tl_news_archive.title', 'sql' => "int(10) unsigned NOT NULL default 0", 'relation' => ['type' => 'belongsTo', 'load' => 'lazy']],
        'sorting' => [
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ],
        'tstamp' => ['sql' => "int(10) unsigned NOT NULL default 1"],
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
        'published' => ['toggle' => true, 'filter' => true, 'inputType' => 'checkbox', 'eval' => ['doNotCopy' => true], 'sql' => ['type' => 'boolean', 'default' => false]],
        'text' => ['search' => true, 'inputType' => 'textarea', 'eval' => ['mandatory' => true, 'basicEntities' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'], 'explanation' => 'insertTags', 'sql' => "mediumtext NULL"],

        'thema_nr' => ['inputType' => 'text', 'eval' => ['tl_class' => 'w50'],'sql' => "int(10) unsigned NOT NULL default 1"],
        'thema_art' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_art'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => [
                ThemenArt::THEMA,
                ThemenArt::UEBERSCHRIFT,
                ThemenArt::SCHWURBEL,
                ThemenArt::GADGET,
                ThemenArt::TIMETABLE,
                ThemenArt::SCHWURBEL,
                ThemenArt::TEXT,
                ThemenArt::EXPERIMENT,
                ThemenArt::SNACKABLESCIENCE,
                ThemenArt::BEGRUESSUNG,
                ThemenArt::KOMMENTAR,
                ThemenArt::HAUSMEISTEREI
            ],
            'eval' => ['tl_class' => 'w50 wizard'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'link' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_link'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'sql' => "varchar(512) NOT NULL default ''",
        ]

    ]
];
