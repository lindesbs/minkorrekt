<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_minkorrekt_folgen'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
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
            'fields' => ['episode', 'title'],
            'format' => '%s :: %s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ]
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],

            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => ['href' => 'act=show', 'icon' => 'show.svg'],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,newsId,episode,content',
    ],
    'fields' => [
        'id' => ['label' => ['ID'], 'sql' => 'int(10) unsigned NOT NULL auto_increment'],

        'sorting' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'col1 width4'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
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
        'newsId' => [
            'exclude' => true,
            'inputType' => 'select',
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
            'foreignKey' => 'tl_news.headline',
        ],
        'experimentId' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
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
    ],
];
