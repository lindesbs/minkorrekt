<?php declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use lindesbs\minkorrekt\Constants\BearbeitungsStatus;

$arrBlocks = explode(';', (string)$GLOBALS['TL_DCA']['tl_news']['palettes']['default']);

$arrNewBlocks = [];
$arrNewBlocks[] = array_shift($arrBlocks);
$arrNewBlocks[] = '{minkorrektLabel},minkorrekt_wip';
$arrNewBlocks = array_merge($arrNewBlocks, $arrBlocks);

$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = implode(';', $arrNewBlocks);

$GLOBALS['TL_DCA']['tl_news']['fields']['minkorrekt_wip'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news']['minkorrekt_thema_folge'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'radio',
    'options' => [
        BearbeitungsStatus::UNBEARBEITET,
        BearbeitungsStatus::IN_BEARBEITEUNG,
        BearbeitungsStatus::ABGENOMMEN,

    ],
    'eval' => ['tl_class'=>'w50'],
    'sql' => sprintf("varchar(16) NOT NULL default '%s'", BearbeitungsStatus::UNBEARBEITET)
];


