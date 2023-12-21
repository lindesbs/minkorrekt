<?php declare(strict_types=1);


use Contao\Controller;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenFilter;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenListe;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenPagination;

$GLOBALS['TL_DCA']['tl_content']['palettes'][ContentMinkorrektFolgenListe::TYPE] =
    '{type_legend},type,headline;minkorrekt_lister_item,perPage;jumpTo'
;

$GLOBALS['TL_DCA']['tl_content']['palettes'][ContentMinkorrektFolgenFilter::TYPE] =
    '{type_legend},type,headline'
;


$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_lister_item'] =[
    'inputType'               => 'select',
    'options_callback' => static function () {
        return Controller::getTemplateGroup('minkorrekt_lister_item_');
    },
    'eval'                    => ['includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'],
    'sql'                     => "varchar(64) COLLATE ascii_bin NOT NULL default ''"
];


$GLOBALS['TL_DCA']['tl_content']['fields']['jumpTo'] =[
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => ['fieldType'=>'radio', 'tl_class'=>'clr'],
    'sql'                     => "int(10) unsigned NOT NULL default 0",
    'relation'                => ['type'=>'hasOne', 'load'=>'lazy']
];