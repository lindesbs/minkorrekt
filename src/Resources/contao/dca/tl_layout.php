<?php

declare(strict_types=1);


$GLOBALS['TL_DCA']['tl_layout']['fields']['alias'] = [
    'inputType' => 'text',
    'sql' => "varchar(32) NOT NULL default ''"
];