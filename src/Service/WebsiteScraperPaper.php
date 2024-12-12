<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Service;

use Contao\StringUtil;
use lindesbs\minkorrekt\Factory\WebscraperPaperDecoderFactory;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

class WebsiteScraperPaper
{
    public function __construct(
        private readonly WebscraperPaperDecoderFactory $webscraperItemdecoderFactory,
        private array                                  $unknownMeta=[]
    )
    {
    }

    public function getUnknownMeta(): array
    {
        return $this->unknownMeta;
    }


    public function scrape(MinkorrektPaperModel $paper): MinkorrektPaperModel
    {
        try {
            $cacheClient = new FilesystemAdapter(
                'minkorrekt',
                86400,
                'var/cache/minkorrekt/paper'
            );

            $cacheKey = 'WEPAGE_' . StringUtil::generateAlias($paper->url);

            $cacheItem = $cacheClient->getItem($cacheKey);

            if (!$cacheItem->isHit()) {

                $browser = new HttpBrowser(HttpClient::create());
                $crawler = $browser->request("GET", $paper->url, [
                    'timeout' => 45
                ]);


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
                    if ($meta['content']) {
                        $arrMeta[$meta['name']][substr(md5((string) $meta['content']), 0, 8)] = $meta['content'];
                    }
                }
            }

            if (count($arrMeta) > 0) {
                $this->unknownMeta = array_merge($arrMeta, $this->unknownMeta);
                ksort($this->unknownMeta);

            }
        } catch (\Exception) {
        }

        return $paper;
    }

}
