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
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'texts', template: 'mi_list_element', type: 'minkorrekt_liste')]
class ListElementController extends AbstractContentElementController
{


    public function __construct(
        private readonly PodcastEpisodeRepository $podcastEpisodeRepository
    )
    {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {

        $task = new PodcastEpisode();
        $form = $this->createForm(ListeFilterForm::class, $task);

        $template->form = $form->createView();

        $template->data = $this->podcastEpisodeRepository->findAll();


        return $template->getResponse();
    }
}