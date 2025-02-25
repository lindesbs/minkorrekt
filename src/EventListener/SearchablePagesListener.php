<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\EventListener;


use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\Environment;
use Contao\PageModel;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;


class SearchablePagesListener
{

    public function __construct(
        private readonly PodcastEpisodeRepository $podcastEpisodeRepository,
        private readonly ContentUrlGenerator      $urlGenerator,
    ) {
    }

    public function onGetSearchablePages(array $pages, $rootId, $isSitemap): array
    {
        $podcastEpisodes = $this->podcastEpisodeRepository->findAll();

        $objJumpTo = PageModel::findById(10);
        $jumpTo = $this->urlGenerator->generate($objJumpTo);

        $customPages=[];
        foreach ($podcastEpisodes as $episode) {
            $customPages[] = sprintf('%s/%s/%s',
                Environment::get('base'),
                $jumpTo,
                $episode->getSlug()
            );
        }

        dd($customPages);

        return array_merge($pages, $customPages);
    }

}