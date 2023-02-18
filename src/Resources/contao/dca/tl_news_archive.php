<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['alias'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_news_archive']['alias'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['rgxp' => 'number', 'tl_style' => 'w50'],
    'sql' => "varchar(32) NOT NULL default ''"
];