<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['alias'] = [
    'inputType' => 'text',
    'sql' => "varchar(32) NOT NULL default ''"
];