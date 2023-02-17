<?php


use lindesbs\minkorrekt\ContentElement\ContentMinkorrektNewsElement;
use lindesbs\minkorrekt\src\Classes\PaperRebuild;

$GLOBALS['TL_CTE']['minkorrekt']['minkorrekt_thema'] = ContentMinkorrektNewsElement::class;

$GLOBALS['BE_MOD']['minkorrekt'] = [
    'publisher' => [
        'tables' => ['tl_minkorrekt_publisher']
    ],
    'paper' => [
        'tables' => ['tl_minkorrekt_paper'],
        'rebuild' => [PaperRebuild::class, 'rebuild']
    ]

];

