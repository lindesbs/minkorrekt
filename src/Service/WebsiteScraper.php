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

    public function scrape(MinkorrektPaperModel $paper): void
    {
        $filesystemAdapter = new FilesystemAdapter('minkorrekt');
        $cacheKey = 'WEPAGE_' . StringUtil::generateAlias($paper->url);

        $cacheItem = $filesystemAdapter->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $browser = new HttpBrowser(HttpClient::create());
            $crawler = $browser->request("GET", $paper->url);

            $cacheItem->set($crawler);
            $cacheItem->expiresAfter(86400);

            $filesystemAdapter->save($cacheItem);
        } else {
            $crawler = $cacheItem->get();
        }


        $metaTags = $crawler->filter('head meta')->each(static fn($node) => [
            'name' => $node->attr('name'),
            'content' => $node->attr('content'),
        ]);

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
    }

}
