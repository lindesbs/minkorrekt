<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Factory;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class WebscraperItemdecoderFactory
{
    private iterable $importer;

    public function __construct(iterable $importer)
    {
        $this->importer = $importer;
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