<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use Contao\DataContainer;
use Contao\DC_Table;
use lindesbs\minkorrekt\Classes\PaperSubmit;
use Symfony\Component\Intl\Languages;

$GLOBALS['TL_DCA']['tl_minkorrekt_paper'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'onsubmit_callback' => [
            [
                PaperSubmit::class,
                'scrape',
            ],
        ],
        'ctable' => [
            'tl_minkorrekt_paper_tags',
            'tl_minkorrekt_paper_creator'
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
                'published' => 'index',
                'pid' => 'index',
            ],
        ],
    ],
    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTABLE,
            'panelLayout' => 'filter;search,limit;sort',
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
            ]
        ],
        'operations' => [
            'editheader' => ['href' => 'act=edit', 'icon' => 'edit.svg'],
            'edit' => [
                'href' => 'table=tl_minkorrekt_paper_creator',
                'icon' => 'person.svg',
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
            'published' => [
                'href'                => 'act=toggle&amp;field=published',
                'icon'                => 'visible.svg',
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
        'default' => '{title_legend},pid,title,citation_title,alias,url;copyright,rights,rightsAgent;' .
            'description,thePublisher,published,status;license,onlineAt,receivedAt,size,' .
            'acceptedAt,publishedAt,doi,doiurl,citation_springer_api_url,subjects,screenshotSRC,screenshotFullpageSRC;tlContentId,' .
            'tlNewsId;price;paperType,language,twitter;citation_firstpage,citation_lastpage,citation_article_type,' .
            'citation_pdf_url,citation_fulltext_html_url,citation_issn',
    ],
    'fields' => [
        'id' => ['label' => ['ID'], 'sql' => 'int(10) unsigned NOT NULL auto_increment'],

        'pid' => [
            'foreignKey' => 'tl_minkorrekt_publisher.title',
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'sorting' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'col1 width4'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'citation_title' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['decodeEntities' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'citation_pdf_url' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'citation_springer_api_url' => [

            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'citation_fulltext_html_url' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 2048,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'citation_issn' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'copyright' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['decodeEntities' => true, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'rights' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['decodeEntities' => true, 'maxlength' => 255],
            'sql' => "text NULL",
        ],
        'rightsagent' => [
            'exclude' => true,
            'inputType' => 'text',
            'search' => true,
            'eval' => ['decodeEntities' => true, 'maxlength' => 255],
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
                'readonly' => true,
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'published' => [
            'toggle' => true,
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],

        'citation_firstpage' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(3) unsigned NOT NULL default 0',
        ],

        'citation_lastpage' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural'],
            'sql' => 'int(3) unsigned NOT NULL default 0',
        ],

        'size' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural', 'tl_class' => 'w50'],
            'sql' => 'int(11) unsigned NOT NULL default 0',
        ],

        'thePublisher' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_minkorrekt_publisher.title',
            'eval' => ['mandatory' => true, 'chosen' => true, 'tl_class' => 'clr'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'status' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'radio',
            'options' => ['UNTOUCHED', 'INCHECK', 'VERIFICATION', 'VERIFIED'],
            'eval' => ['multiple' => false, 'includeBlankOption' => true, 'chosen' => true],
            'sql' => "varchar(255) NOT NULL default 'UNTOUCHED'",
        ],
        'url' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048],
            'sql' => 'text NULL',
        ],
        'license' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => ['OPEN', 'CLOSED', 'UNKNOWN'],
            'sql' => "varchar(16) NOT NULL default 'UNKNOWN'",
        ],
        'citation_article_type' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => [
                'Analysis',
                'Article',
                'Articles',
                'Brief Communication',
                'Brief Report',
                'Comment',
                'Letter',
                'Original Article',
                'Other',
                'proceedings',
                'Protocol',
                'Regular Article',
                'Research',
                'Research Article',
                'UNKNOWN'
            ],
            'sql' => "varchar(255) NOT NULL default 'UNKNOWN'",
        ],

        'paperType' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => ['OriginalPaper', 'Text', 'research-article', 'UNKNOWN'],
            'sql' => "varchar(64) NOT NULL default 'UNKNOWN'",
        ],
        'twitter' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "varchar(64) NOT NULL default 'UNKNOWN'",
        ],
        'receivedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard clr'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'acceptedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard clr'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'publishedAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard clr'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'onlineAt' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard clr'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'doi' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'doiurl' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 2048, 'tl_class' => 'w50 url clr'],
            'sql' => 'text NULL',
        ],

        'subjects' => [
            'exclude' => true,
            'inputType' => 'select',
            'foreignKeys' => 'tl_minkorrekt_paper_tags.name',
            'eval' => ['multiple' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'clr'],
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
        'tlContentId' => [
            'label' => ['Content ID'],
            'inputType' => 'select',
            'foreignKey' => 'tl_content.text',
            'eval' => ['readonly' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'tlNewsId' => [
            'label' => ['News ID'],
            'inputType' => 'select',
            'foreignKey' => 'tl_news.headline',
            'eval' => ['readonly' => true],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'price' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'decodeEntities' => true, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(11) NOT NULL default ''",
        ],
        'language' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'select',
            'options' => Languages::getNames('de'),
            'eval' => [
                'chosen' => true,
                'decodeEntities' => true,
                'tl_class' => 'w50 clr',
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'description' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['decodeEntities' => true],
            'sql' => 'text NULL',
        ],
    ],
];

//
//foreach ($GLOBALS['TL_DCA']['tl_minkorrekt_paper']['fields'] as $fieldKey => $FieldValue) {
//    echo sprintf('
//    <trans-unit id="tl_minkorrekt_paper.%1$s.0">
//                <target>%1$s</target>
//            </trans-unit>
//            <trans-unit id="tl_minkorrekt_paper.%1$s.1">
//                <target>%1$s</target>
//            </trans-unit>
//            ', $fieldKey);
//}
//
