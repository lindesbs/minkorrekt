<?php

namespace lindesbs\minkorrekt\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Input;
use Google\Service\YouTubeAnalytics\EmptyResponse;
use lindesbs\minkorrekt\Models\MinkorrektFolgenInhaltModel;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\minkorrekt\Service\ZeitUmrechner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(ContentFolgenDetail::TYPE, category: 'minkorrekt')]
class ContentFolgenDetail extends AbstractContentElementController
{
    public const TYPE = 'content_minkorrekt_folgen_detail';

    public function __construct(
        private readonly ZeitUmrechner $zeitUmrechner
    )
    {
    }


    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->headline = unserialize($model->headline);

        $objDetail = MinkorrektFolgenModel::findByIdOrAlias(Input::get('auto_item'));

        if ($objDetail) {
            $objFolgenItems = MinkorrektFolgenInhaltModel::findBy('pid', $objDetail->id);


            dd($objFolgenItems);
            $template->keys = array_keys($objDetail->row());
            $template->items = $objFolgenItems;
            $template->content = $objDetail;
            return $template->getResponse();
        }

        return new Response();

    }
}