<?php declare(strict_types=1);


use Contao\Controller;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenFilter;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenListe;
use lindesbs\minkorrekt\ContentElement\ContentMinkorrektFolgenPagination;

$GLOBALS['TL_DCA']['tl_content']['palettes'][ContentMinkorrektFolgenListe::TYPE] =
    '{type_legend},type,headline;minkorrekt_lister_item,perPage'
;

$GLOBALS['TL_DCA']['tl_content']['palettes'][ContentMinkorrektFolgenFilter::TYPE] =
    '{type_legend},type,headline'
;


$GLOBALS['TL_DCA']['tl_content']['fields']['minkorrekt_lister_item'] =[
    'inputType'               => 'select',
    'options_callback' => static function () {
        return Controller::getTemplateGroup('minkorrekt_lister_item_');
    },
    'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(64) COLLATE ascii_bin NOT NULL default ''"
];