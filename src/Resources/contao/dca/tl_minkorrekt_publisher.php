<?php


use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_minkorrekt_publisher'] = array
(
    // Config
    'config' => array
    (
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ]
        ]
    ),

    // List
    'list' => array
    (
        'sorting' => [
            'mode' => DataContainer::MODE_UNSORTED,
            'panelLayout' => 'filter;search'
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations' => array
        (
            'editheader' => array
            (
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ),
            'copy' => array
            (
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ),
            'cut' => array
            (
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ),
            'delete' => array
            (
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array
            (
                'href' => 'act=show',
                'icon' => 'show.svg'
            )
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title, url,screenshotSRC,screenshotFullpageSRC'
    ),
    'fields' => array
    (
        'id' => array
        (
            'label' => array('ID'),
            'search' => true,
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'sorting' => array
        (
            'sql' => "int(10) unsigned NOT NULL default 0"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default 0"
        ),
        'title' => array
        (
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => array('mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'url' => array
        (
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'w50'),
            'sql' => "text NULL"
        ),
        'screenshotSRC' => array
        (
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
            'sql' => "binary(16) NULL"
        ),
        'screenshotFullpageSRC' => array
        (
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
            'sql' => "binary(16) NULL"
        ),

    )
);