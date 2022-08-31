<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\ContentElement;

class ContentMinkorrektNewsElement extends \ContentElement
{
    /**
     * @var string Template
     */
    protected $strTemplate = 'ce_text';



    public function compile()
    {
        $template = new \BackendTemplate('be_wildcard');

        $template->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['helloWorld'][0]).' ###';
        $template->title = $this->headline;
        $template->id = $this->id;
        $template->link = $this->name;
        $template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

        return $template->parse();
    }

}
