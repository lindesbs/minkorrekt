<?php

namespace lindesbs\minkorrekt\Interface;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

interface WebscraperPaperDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool;
}