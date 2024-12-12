<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use lindesbs\minkorrekt\Interface\WebscraperPaperDecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class TwitterDecoder implements WebscraperPaperDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool
    {
        $arrNotYetImplemented = [
            'twitter:card',
            'twitter:image:alt',
            'twitter:title',
            'twitter:description',
            'twitter:image',
            'twitter:site'
        ];

        $strKey = strtolower($strKey);

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }


        if ('twitter:site' === $strKey) {
            $paperModel->twitter = $strContent;
            return true;
        }

        return false;
    }
}