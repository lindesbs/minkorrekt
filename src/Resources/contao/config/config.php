<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use lindesbs\minkorrekt\Classes\PaperRebuild;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektNewsElement;

$GLOBALS['BE_MOD']['minkorrekt'] = [
    'publisher' => [
        'tables' => ['tl_minkorrekt_publisher', 'tl_minkorrekt_paper'],
    ],
    'paper' => [
        'tables' => ['tl_minkorrekt_paper', 'tl_minkorrekt_paper_creator'],
        'rebuild' => [PaperRebuild::class, 'rebuild'],
    ],
    'tags' => [
        'tables' => ['tl_minkorrekt_paper_tags'],
    ],
];


$GLOBALS['TL_CTE']['minkorrekt']['minkorrekt_thema'] = ContentMinkorrektNewsElement::class;
