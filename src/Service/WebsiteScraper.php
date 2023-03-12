<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Service;

use Contao\StringUtil;
use lindesbs\minkorrekt\Factory\WebscraperItemdecoderFactory;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

class WebsiteScraper
{


    public function __construct(
        private readonly WebscraperItemdecoderFactory $webscraperItemdecoderFactory
    ) {
    }

    public function scrape(MinkorrektPaperModel $paper): MinkorrektPaperModel
    {
        $cacheClient = new FilesystemAdapter(
            'minkorrekt',
            86400,
            'var/cache/minkorrekt'
        );

        $cacheKey = 'WEPAGE_' . StringUtil::generateAlias($paper->url);

        $cacheItem = $cacheClient->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $browser = new HttpBrowser(HttpClient::create());
            $crawler = $browser->request("GET", $paper->url);

            $metaTags = $crawler->filter('head meta')->each(
                static fn($node) => [
                'name' => $node->attr('name'),
                'content' => $node->attr('content'),
                ]
            );

            $cacheItem->set($metaTags);

            $cacheClient->save($cacheItem);
            $cacheClient->commit();
        } else {
            $metaTags = $cacheItem->get();
        }


        $arrMeta = [];

        foreach ($metaTags as $meta) {
            if ((!$meta['name']) || (!$meta['content'])) {
                continue;
            }

            $bFound = $this->webscraperItemdecoderFactory->walkThroughDecoder(
                $meta['name'],
                $meta['content'],
                $paper
            );


            $paper->save();

            if (!$bFound) {
                $arrMeta[$meta['name']] = $meta['name'];
            }
        }

        if (count($arrMeta) > 0) {
            ksort($arrMeta);
            //dd($arrMeta);
        }

        return $paper;
    }

}
