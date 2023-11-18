<?php

namespace lindesbs\minkorrekt\Interface;

use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

interface WebscraperPublisherDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPublisherModel $publisherModel): bool;
}