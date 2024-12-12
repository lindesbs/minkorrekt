<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_minkorrekt_paper_creator'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ptable' => 'tl_minkorrekt_paper',
        'markAsCopy' => 'title',
        'onload_callback' => [],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
            ],
        ],
    ],
    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_UNSORTED,
            'panelLayout' => 'filter;search',
        ],
        'label' => [
            'fields' => ['alias', 'name'],
            'format' => '%s %s',
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
    'palettes' => ['default' => '{title_legend},name,alias'],
    'fields' => [
        'id' => ['label' => ['ID'], 'sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'sorting' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],

        'pid' => [
            'sorting' => true,
            'filter' => true,
            'foreignKey' => 'tl_minkorrekt_paper.title',
            'sql'                     => "int(10) unsigned NOT NULL default 0",
            'relation'                => ['type'=>'belongsTo', 'load'=>'lazy']
        ],
        'name' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
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
    ],
];
