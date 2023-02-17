<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_minkorrekt_paper'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'onload_callback' => [],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
                'published' => 'index',
            ],
        ]
    ],
    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_UNSORTED,
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['status', 'alias', 'thePublisher', 'title'],
            'format' => '%s %s : %s :: %s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
            'rebuild' => ['href' => 'key=rebuild', 'icon' => 'su.svg'],
        ],
        'operations' => [
            'edit' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ],
            'show' => ['href' => 'act=show', 'icon' => 'show.svg']
        ]
    ],
    // Palettes
    'palettes' => ['default' => '{title_legend},title,alias,thePublisher,published,status;url,license,receivedAt,acceptedAt,publishedAt,doiurl,subjects,screenshotSRC,screenshotFullpageSRC;tlContentId,tlNewsId'],
    'fields' => [
        'id' => ['label' => ['ID'], 'sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'sorting' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'alias' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => [
                'rgxp' => 'alias',
                'doNotCopy' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
                'readonly' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'published' => [
            'exclude' => true,
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'thePublisher' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_minkorrekt_publisher.title',
            'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'clr'],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'status' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'options' => ['UNTOUCHED', 'INCHECK', 'VERIFICATION', 'VERIFIED'],
            'eval' => ['multiple' => true],
            'sql' => "varchar(255) NOT NULL default 'UNTOUCHED'",
        ],
        'url' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048],
            'sql' => 'text NULL'
        ],
        'license' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => ['OPEN', 'CLOSED', 'UNKNOWN'],
            'sql' => "varchar(16) NOT NULL default 'UNKNOWN'",
        ],
        'receivedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'acceptedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'publishedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'doiurl' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'w50 url'],
            'sql' => 'text NULL'
        ],

        'subjects' => ['exclude' => true, 'inputType' => 'text', 'eval' => [], 'sql' => 'text NULL'],
        'screenshotSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL'
        ],
        'screenshotFullpageSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL'
        ],
        'tlContentId' => [
            'label' => ['Content ID'],
            'inputType' => 'select',
            'foreignKey' => 'tl_content.text',
            'eval' => ['readonly' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0'
        ],
        'tlNewsId' => [
            'label' => ['News ID'],
            'inputType' => 'select',
            'foreignKey' => 'tl_news.headline',
            'eval' => ['readonly' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0'
        ]
    ],
];
