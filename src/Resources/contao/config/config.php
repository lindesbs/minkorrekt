<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperTagsModel;
use lindesbs\minkorrekt\Models\MinkorrektFolgenInhaltModel;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

$GLOBALS['BE_MOD']['minkorrekt'] = [
    'folgen' => [
        'tables' => ['tl_minkorrekt_folgen', 'tl_minkorrekt_folgen_inhalt']
    ],
    'publisher' => [
        'tables' => ['tl_minkorrekt_publisher', 'tl_minkorrekt_paper'],
    ],
    'paper' => [
        'tables' => ['tl_minkorrekt_paper', 'tl_minkorrekt_paper_creator']
    ],
    'tags' => [
        'tables' => ['tl_minkorrekt_paper_tags'],
    ],
];

$GLOBALS['TL_MODELS']['tl_minkorrekt_folgen'] = MinkorrektFolgenModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_folgen_inhalt'] = MinkorrektFolgenInhaltModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper_creator'] = MinkorrektPaperCreatorModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper'] = MinkorrektPaperModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper_tags'] = MinkorrektPaperTagsModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_publisher'] = MinkorrektPublisherModel::class;


