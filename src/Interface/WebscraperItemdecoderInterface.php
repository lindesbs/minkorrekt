<?php

namespace lindesbs\minkorrekt\Interface;

use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

interface WebscraperItemdecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool;
}