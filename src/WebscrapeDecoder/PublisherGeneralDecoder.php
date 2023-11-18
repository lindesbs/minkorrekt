<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use Contao\StringUtil;
use lindesbs\minkorrekt\Interface\WebscraperPaperDecoderInterface;
use lindesbs\minkorrekt\Interface\WebscraperPublisherDecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

class PublisherGeneralDecoder implements WebscraperPublisherDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPublisherModel $publisherModel): bool
    {
        $arrNotYetImplemented = [
        ];

        $strKey = strtolower($strKey);

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }

        if (strlen(trim($strContent))==0)
        {
            return false;
        }

        if ('title' === $strKey) {
            $publisherModel->title = $strContent;

            return true;
        }


        if ('twitter:title' === $strKey) {
            $publisherModel->title = $strContent;

            return true;
        }

        if ('application-name' === $strKey) {
            $publisherModel->title = $strContent;

            return true;
        }

        if ('dc.rights' === $strKey) {
            $publisherModel->rights = $strContent;

            return true;
        }

        if ('description' === $strKey) {
            $publisherModel->description = $strContent;

            return true;
        }



        return false;
    }
}