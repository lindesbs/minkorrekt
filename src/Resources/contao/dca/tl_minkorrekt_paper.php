<?php


use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_minkorrekt_paper'] = array
(
    // Config
    'config' => array
    (
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'onload_callback' => [],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
                'published' => 'index'
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
            'fields' => ['status', 'alias', 'thePublisher', 'title'],
            'format' => '%s %s : %s :: %s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ],
            'rebuild' => array
            (
                'href' => 'key=rebuild',
                'icon' => 'su.svg'
            )
        ],
        'operations' => array
        (
            'edit' => array
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
                'attributes' => 'onclick="Backend.getScrollOffset()"'
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
        'default' => '{title_legend},title,alias,thePublisher,published,status;url,license,receivedAt,acceptedAt,publishedAt,doiurl,subjects,screenshotSRC,screenshotFullpageSRC;tlContentId,tlNewsId'
    ),
    'fields' => array
    (
        'id' => array
        (
            'label' => array('ID'),
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
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'alias' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'maxlength' => 255, 'tl_class' => 'w50',
                'readonly' => true],
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'published' => [
            'exclude' => true,
            'toggle' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''"
        ],
        'thePublisher' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_minkorrekt_publisher.title',
            'eval' => array('mandatory' => true, 'chosen' => true, 'tl_class' => 'clr'),
            'sql' => "varchar(32) NOT NULL default ''"
        ],
        'status' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'options' => ['UNTOUCHED', 'INCHECK', 'VERIFICATION', 'VERIFIED'],
            'eval' => ['multiple' => true],
            'sql' => "varchar(255) NOT NULL default 'UNTOUCHED'"
        ],
        'url' => array
        (
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048),
            'sql' => "text NULL"
        ),
        'license' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => ['OPEN', 'CLOSED'],
            'sql' => "varchar(16) NOT NULL default 'UNTOUCHED'"
        ],
        'receivedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''"
        ],
        'acceptedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''"
        ],

        'publishedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(11) NOT NULL default ''"
        ],
        'doiurl' => array
        (
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'w50 url'),
            'sql' => "text NULL"
        ),

        'subjects' => array
        (
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [],
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

        'tlContentId' => array
        (
            'label' => array('Content ID'),
            'inputType' => 'select',
            'foreignKey' => 'tl_content.text',
            'eval' => ['readonly' => true],
            'sql' => "int(10) unsigned NOT NULL default 0"
        ),

        'tlNewsId' => array
        (
            'label' => array('News ID'),
            'inputType' => 'select',
            'foreignKey' => 'tl_news.headline',
            'eval' => ['readonly' => true],
            'sql' => "int(10) unsigned NOT NULL default 0"
        ),
    )
);