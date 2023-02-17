<?php declare(strict_types=1);

use lindesbs\ContaoTools\Classes\DCA;
use lindesbs\ContaoTools\Classes\DCAType;

$arrBlocks = explode(";", (string)$GLOBALS['TL_DCA']['tl_content']['palettes']['text']);

$arrNewBlocks = [];
$arrNewBlocks[] = array_shift($arrBlocks);
$arrNewBlocks[] = "{minkorrektLabel},minkorrekt_thema_art,minkorrekt_thema_folge,minkorrekt_thema_nummer";
$arrNewBlocks = array_merge($arrNewBlocks, $arrBlocks);

$GLOBALS['TL_DCA']['tl_content']['palettes']['minkorrekt_thema'] = implode(";", $arrNewBlocks);


DCA::DCA('tl_content', true,
    DCA::Group('minkorrekt', [
        DCA::Field('minkorrekt_thema_art', DCAType::SELECT, [
            'THEMA',
            'UEBERSCHRIFT',
            'SCHWURBEL',
            'GADGET',
            'TIMETABLE',
            'ANFANG',
            'ADRESSEN',
            'TEXT',
            'EXPERIMENT'
        ]),
        DCA::Field('minkorrekt_thema_folge', DCATYPE::TEXT),
        DCA::Field('minkorrekt_thema_nummer', DCAType::TEXT),
    ],
    )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_art'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_folge'],
    'exclude'                   => true,
    'inputType'                 => 'select',
    'options'                   => [
        'THEMA',
        'UEBERSCHRIFT',
        'SCHWURBEL',
        'GADGET',
        'TIMETABLE',
        'ANFANG',
        'ADRESSEN',
        'TEXT',
        'EXPERIMENT'
    ],
    'eval'                      => [],
    'sql'                       => "varchar(32) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_folge'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_folge'],
    'exclude'                   => true,
    'inputType'                 => 'text',
    'eval'                      => array('rgxp'=>'number', "tl_style"=>"w50"),
    'sql'                       => "int(10) unsigned NOT NULL default '0'"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_nummer'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_nummer'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array('rgxp' => 'number', "tl_style" => "w50"),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);


