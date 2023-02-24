<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

$arrBlocks = explode(';', (string)$GLOBALS['TL_DCA']['tl_content']['palettes']['text']);

$arrNewBlocks = [];
$arrNewBlocks[] = array_shift($arrBlocks);
$arrNewBlocks[] = '{minkorrektLabel},minkorrekt_thema_art,minkorrekt_thema_folge,minkorrekt_thema_nummer';
$arrNewBlocks = array_merge($arrNewBlocks, $arrBlocks);

$GLOBALS['TL_DCA']['tl_content']['palettes']['minkorrekt_thema'] = implode(';', $arrNewBlocks);

$GLOBALS['TL_DCA']['tl_content']['fields']['alias'] = [
    'inputType' => 'text',
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_art'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_folge'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => [
        'THEMA',
        'UEBERSCHRIFT',
        'SCHWURBEL',
        'GADGET',
        'TIMETABLE',
        'ANFANG',
        'ADRESSEN',
        'TEXT',
        'EXPERIMENT',
    ],
    'eval' => [],
    'sql' => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_folge'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_folge'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'number', 'tl_style' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_thema_nummer'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['minkorrekt_thema_nummer'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'number', 'tl_style' => 'w50'],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];
