<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use lindesbs\minkorrekt\Interface\WebscraperPaperDecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

class GlobalDecoder implements WebscraperPaperDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool
    {
        $arrNotYetImplemented = [

            'applicable-device',
            'viewport',
            'msapplication-TileColor',
            'msapplication-config',
            'theme-color',
            'application-name',
            'robots',
            'access',
            'DOI',
            'access_endpoint',
            'format-detection',
            'pbContext',
            'apple-itunes-app',
            'google-play-app',
            'asset_id',
            'asset_type',
            'journal_id',
        ];

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }

        if ('journal_id' === $strKey) {
            $objJournal = MinkorrektPublisherModel::findOneBy('journal_id', $strContent);

            if (!$objJournal) {
                $objJournal = new MinkorrektPublisherModel();
                $objJournal->journal_id = $strContent;
                $objJournal->title = sprintf('--NOT YET SET -- %s', $paperModel->alias);
                $objJournal->save();
            }
            $paperModel->pid = $objJournal->id;

            return true;
        }

        if ('description' === strtolower($strKey)) {
            $paperModel->description = $strContent;

            return true;
        }

        if ('size' === $strKey) {
            $paperModel->size = (int)$strContent;

            return true;
        }

        return false;
        ;
    }
}