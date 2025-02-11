<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use lindesbs\minkorrekt\Entity\PodcastEpisode;
use lindesbs\minkorrekt\FormType\ListeFilterForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'texts')]
class ListElementController extends AbstractContentElementController
{
    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

        $task = new PodcastEpisode();
        $form = $this->createForm(ListeFilterForm::class, $task);

        $template->form = $form;

        return $template->getResponse();
    }
}