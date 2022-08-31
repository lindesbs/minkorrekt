<?php declare(strict_types=1);

$arrBlocks = explode(";", $GLOBALS['TL_DCA']['tl_content']['palettes']['text']);

$arrNewBlocks=[];
$arrNewBlocks[] = array_shift($arrBlocks);
$arrNewBlocks[] = "{minkorrektLabel},minkorrekt_thema_art,minkorrekt_thema_folge,minkorrekt_thema_nummer";
$arrNewBlocks = array_merge($arrNewBlocks,$arrBlocks);

$GLOBALS['TL_DCA']['tl_content']['palettes']['minkorrekt_thema'] = implode(";", $arrNewBlocks);



//dd($GLOBALS['TL_DCA']['tl_content']);
/**
 * Fields
 */


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
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_nummer'],
    'exclude'                   => true,
    'inputType'                 => 'text',
    'eval'                      => array('rgxp'=>'number', "tl_style"=>"w50"),
    'sql'                       => "int(10) unsigned NOT NULL default '0'"
);