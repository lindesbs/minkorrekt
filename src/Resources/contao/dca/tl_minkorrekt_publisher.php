<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;
use Contao\FrontendUser;
use Symfony\Component\Intl\Languages;

$GLOBALS['TL_DCA']['tl_minkorrekt_publisher'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_minkorrekt_paper'],
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'journal_id' => 'index',
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
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'editheader' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'edit' => [
                'href' => 'table=tl_minkorrekt_paper',
                'icon' => 'layout.svg',
            ],
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
    'palettes' => ['default' => '{title_legend},title,journal_id,url,language,screenshotSRC,screenshotFullpageSRC;editor'],
    'fields' => [
        'id' => ['label' => ['ID'], 'search' => true, 'sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'sorting' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'journal_id' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'url' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 2048,
                'tl_class' => 'w50',
            ],
            'sql' => 'text NULL',
        ],

        'screenshotSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'screenshotFullpageSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'language' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => Languages::getNames('de'),
            'eval' => [
                'chosen' => true,
                'decodeEntities' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'editor' => [
            'default' => FrontendUser::getInstance()->id,
            'exclude' => true,
            'search' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => DataContainer::SORT_ASC,
            'inputType' => 'select',
            'foreignKey' => 'tl_member.lastname',
            'eval' => [
                'doNotCopy' => true,
                'chosen' => true,
                'multiple' => true,
                'mandatory' => false,
                'includeBlankOption' => true,
                'tl_class' => 'w50'
            ],

            'sql' => "varchar(64) NOT NULL default ''",
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
    ],
];
