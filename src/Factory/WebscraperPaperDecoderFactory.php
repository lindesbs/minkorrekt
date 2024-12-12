<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Factory;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class WebscraperPaperDecoderFactory
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
     * @param MinkorrektPaperModel $paperModel
     * @return bool
     */
    public function walkThroughDecoder(string $strKey, $strContent, MinkorrektPaperModel $paperModel): bool
    {
        foreach ($this->importer as $import) {
            $bFound = $import->decode($strKey, $strContent, $paperModel);

            if ($bFound) {
                return true;
            }
        }

        return false;
    }

}