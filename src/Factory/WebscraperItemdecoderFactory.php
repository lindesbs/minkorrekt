<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Factory;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class WebscraperItemdecoderFactory
{
    public function __construct(private readonly iterable $importer)
    {
    }

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