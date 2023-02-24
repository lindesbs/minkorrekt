<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Classes;

use Contao\Backend;
use Contao\DataContainer;
use Contao\System;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class PaperSubmit extends Backend
{
    public function scrape(DataContainer $dc = null): void
    {
        if ($dc && ($dc->activeRecord->url)) {
            $thePaper = MinkorrektPaperModel::findByIdOrAlias($dc->activeRecord->id);
            System::getContainer()->get('lindesbs.minkorrekt.websitescrape')->scrape($thePaper);
        }
    }
}
