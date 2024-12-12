<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Factory;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

class WebscraperPublisherDecoderFactory
{
    /**
     * @param iterable $importer
     */
    public function __construct(private readonly iterable $importer)
    {
    }

    /**
     * @param string $strKey
     * @param $strContent
     * @param MinkorrektPaperModel $publisherModel
     * @return bool
     */
    public function walkThroughDecoder(string $strKey, $strContent, MinkorrektPublisherModel $publisherModel): bool
    {
        foreach ($this->importer as $import) {
            $bFound = $import->decode($strKey, $strContent, $publisherModel);

            if ($bFound) {
                return true;
            }
        }

        return false;
    }

}