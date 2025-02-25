<?php declare(strict_types=1);


$GLOBALS['TL_DCA']['tl_content']['palettes']['minkorrekt_liste'] = '{type_legend},type;jumpTo';

$GLOBALS['TL_DCA']['tl_content']['palettes']['minkorrekt_details'] = '{type_legend},type;{text_legend},text;{minkorrekt_legend},minkorrekt';


$GLOBALS['TL_DCA']['tl_content']['fields']['jumpTo'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy']
];