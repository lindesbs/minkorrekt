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
    #[\Override]
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $item = $request->attributes->get('auto_item');
        $objItem = $this->podcastEpisodeRepository->findOneBy(['slug'=> $item]);

        if (Input::get('auto_item') === null) {
            return new Response();
        }

        $template->zeit_vergangen = $objItem->getPubDate()->diff(new \DateTime())->format('%y Jahre, %m Monate und %d Tage');


        if (!$this->container->get('security.token_storage')->getToken()?->getUser()) {


            $template->welcome_message = sprintf('Welcome %s!', $this->container->get('security.token_storage')
                ->getToken()
                ->getUser()
                ->getUsername());
        }

        $template->item= $objItem;
        return $template->getResponse();
    }


}