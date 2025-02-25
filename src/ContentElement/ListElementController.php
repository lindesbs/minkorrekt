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
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\PageModel;
use Contao\Template;
use lindesbs\minkorrekt\Entity\PodcastEpisode;
use lindesbs\minkorrekt\FormType\ListeFilterForm;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\OptionableView;
use Pagerfanta\View\TwitterBootstrap5View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'minkorrekt', template: 'mi_list_element', type: 'minkorrekt_liste')]
class ListElementController extends AbstractContentElementController
{


    public function __construct(
        private readonly PodcastEpisodeRepository $podcastEpisodeRepository,
        private readonly ContentUrlGenerator $urlGenerator,
    ) {
    }

    #[\Override]
    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $task = new PodcastEpisode();
        $form = $this->createForm(ListeFilterForm::class, $task);

        $template->form = $form->createView();

        $pagecount = $request->query->getInt('page_count', 6);

        $elements = $this->podcastEpisodeRepository->createQueryBuilder('p')
            ->getQuery()
            ->getResult();


        $adapter = new ArrayAdapter($elements);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($pagecount);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setAllowOutOfRangePages(true);

        $currentPageResults = $pagerfanta->getCurrentPageResults();
        $template->data = $currentPageResults;

        $defaultView = new TwitterBootstrap5View();
        $routeGenerator = function ($page) {
            $request = Request::createFromGlobals();

            $request->query->set('page', $page);
            $url = $request->getUri();
            $parsedUrl = parse_url($url);
            parse_str($parsedUrl['query'] ?? '', $queryParams);
            $queryParams['page'] = $page;
            $parsedUrl['query'] = http_build_query($queryParams);
            return (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '') .
                ($parsedUrl['host'] ?? '') .
                ($parsedUrl['path'] ?? '') .
                (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');



        };

        $myView1 = new OptionableView($defaultView, ['proximity' => 5]);
        $myView2 = new OptionableView($defaultView, ['proximity' => 2, 'prev_message' => 'Anterior', 'next_message' => 'Siguiente']);

        $template->pagination =  $myView1->render($pagerfanta, $routeGenerator);

        $objJumpTo = PageModel::findById($model->jumpTo);
        $template->jumpTo = $this->urlGenerator->generate($objJumpTo);

        return $template->getResponse();
    }
}