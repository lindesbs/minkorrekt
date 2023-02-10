<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentElement;

class ContentMinkorrektNewsElement extends ContentElement
{
    /**
     * @var string Template
     */
    protected $strTemplate = 'ce_text';


    protected function compile()
    {
        $backendTemplate = new BackendTemplate('be_wildcard');

        $backendTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['helloWorld'][0]) . ' ###';
        $backendTemplate->title = $this->headline;
        $backendTemplate->id = $this->id;
        $backendTemplate->link = $this->name;
        $backendTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

        return $backendTemplate->parse();
    }

}
