<?php

namespace lindesbs\minkorrekt\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(ContentMinkorrektFolgenFilter::TYPE, category: 'minkorrekt')]
class ContentMinkorrektFolgenFilter extends AbstractContentElementController
{
    public const TYPE = 'content_minkorrekt_folgen_filter';

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->headline = unserialize($model->headline);

        return $template->getResponse();
    }
}