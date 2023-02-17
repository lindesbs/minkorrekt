<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

use lindesbs\minkorrekt\Classes\PaperRebuild;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektNewsElement;

$GLOBALS['TL_CTE']['minkorrekt']['minkorrekt_thema'] = ContentMinkorrektNewsElement::class;

$GLOBALS['BE_MOD']['minkorrekt'] = [
    'publisher' => [
        'tables' => ['tl_minkorrekt_publisher'],
    ],
    'paper' => [
        'tables' => ['tl_minkorrekt_paper'],
        'rebuild' => [PaperRebuild::class, 'rebuild'],
    ],
];
