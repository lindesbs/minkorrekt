<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use lindesbs\minkorrekt\Interface\WebscraperItemdecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class PrismDecoder implements WebscraperItemdecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool
    {
        $arrNotYetImplemented = [
            "prism.copyright",
            "prism.doi",
            "prism.endingPage",
            "prism.issn",
            "prism.number",
            "prism.publicationDate",
            "prism.publicationName",
            "prism.rightsAgent",
            "prism.section",
            "prism.startingPage",
            "prism.url",
            "prism.volume"
        ];

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }

        return false;
    }
}