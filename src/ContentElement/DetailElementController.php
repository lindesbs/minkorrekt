<?php declare(strict_types=1);
/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Input;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'minkorrekt', template: 'mi_detail_element', type: 'minkorrekt_details')]
class DetailElementController extends AbstractContentElementController
{

    public function __construct(
        private readonly PodcastEpisodeRepository $podcastEpisodeRepository,
    ) {
    }
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $item = $request->attributes->get('auto_item');
        $objItem = $this->podcastEpisodeRepository->findOneBy(['slug'=> $item]);

        if (Input::get('auto_item') === null) {
            return new Response();
        }

        $template->item= $objItem;
        return $template->getResponse();
    }


}