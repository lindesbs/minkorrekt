<?php

namespace lindesbs\minkorrekt\ContentElement;

use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\PageModel;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\Template;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\minkorrekt\Service\ZeitUmrechner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(ContentMinkorrektFolgenListe::TYPE, category: 'minkorrekt')]
class ContentMinkorrektFolgenListe extends AbstractContentElementController
{
    public const TYPE = 'content_minkorrekt_folgen_liste';

    public function __construct(
        private readonly ZeitUmrechner $zeitUmrechner
    )
    {
    }


    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->headline = unserialize($model->headline);

        $arrItems = MinkorrektFolgenModel::findAll(['order'=>'pubdate DESC']);
        $total = count($arrItems);

        $strContent = '';

        if ($model->perPage > 0) {
            $id = 'page_e' . $model->id;
            $page = (int) (Input::get($id) ?? 1);

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total/$model->perPage), 1)) {
                throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
            }

            $offset = ($page - 1) * $model->perPage;
            $limit = min($model->perPage + $offset, $total);

            $objPagination = new Pagination($total, $model->perPage, Config::get('maxPaginationLinks'), $id);
            $template->pagination = $objPagination->generate("\n  ");
        }

        $i=0;

        foreach ($arrItems as $item) {
            $i++;
            if (($i <= $offset) || ($i>$limit)) {
                continue;
            }

            $objFrontent = new FrontendTemplate($model->minkorrekt_lister_item);

            $objFrontent->setData($item->row());
            $objFrontent->gesamtLaengeDateDiff = $this->zeitUmrechner->convert((int) $item->duration);


            $objDetail = PageModel::findPublishedById($model->jumpTo);
            $href = $objDetail->getFrontendUrl('/'.$item->alias);
            $objFrontent->link = $href;

            $strContent.=$objFrontent->parse();
        }

        $template->content = $strContent;
        return $template->getResponse();
    }
}