<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use lindesbs\minkorrekt\ContentElement\ContentMinkorrektNewsElement;
use lindesbs\minkorrekt\ContentElement\StatisticsContentElement;

$GLOBALS['BE_MOD']['minkorrekt'] = [
    'folgen' => [
        'tables' => ['tl_minkorrekt_folgen','tl_minkorrekt_folgen_inhalt']
    ],
    'themen' => [
        'tables' => ['tl_minkorrekt_themen']
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


$GLOBALS['TL_CTE']['minkorrekt']['minkorrekt_thema'] = ContentMinkorrektNewsElement::class;
$GLOBALS['TL_CTE']['minkorrekt']['minkorrekt_statistics'] = StatisticsContentElement::class;


$GLOBALS['TL_MODELS']['tl_minkorrekt_folgen'] = \lindesbs\minkorrekt\Models\MinkorrektFolgenModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_folgen_inhalt'] = \lindesbs\minkorrekt\Models\MinkorrektFolgenInhaltModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper_creator'] = \lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper'] = \lindesbs\minkorrekt\Models\MinkorrektPaperModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_paper_tags'] = \lindesbs\minkorrekt\Models\MinkorrektPaperTagsModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_publisher'] = \lindesbs\minkorrekt\Models\MinkorrektPublisherModel::class;
$GLOBALS['TL_MODELS']['tl_minkorrekt_themen'] = \lindesbs\minkorrekt\Models\MinkorrektThemenModel::class;