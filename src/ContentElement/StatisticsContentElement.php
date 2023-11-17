<?php

namespace lindesbs\minkorrekt\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\Controller;
use Contao\ModuleEventMenu;
use Contao\System;
use Contao\Template;
use DateTime;
use lindesbs\minkorrekt\Service\GenerateStatistics;

class StatisticsContentElement extends ContentElement
{
    /**
     * @var string Template
     */
    protected $strTemplate = 'ce_statistics_content';

    protected function compile()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $backendTemplate = new BackendTemplate('be_wildcard');

            $backendTemplate->wildcard = '### Minkorrekt Statistik ###';
            $backendTemplate->title = $this->headline;
            $backendTemplate->id = $this->id;
            $backendTemplate->link = $this->name;
            $backendTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $backendTemplate->parse();
        }

        $objStats = \System::getContainer()->get('lindesbs.minkorrekt.generatestatistics');

        /**
 * @var GenerateStatistics $stats
*/
        $objStats->render();

        $this->Template->folgenAnzahl = $objStats->getCountEpisoden();
        $this->Template->ersteFolge = $objStats->getErsteFolge();
        $this->Template->letzteFolge = $objStats->getLetzteFolge();


        $dtNow = new DateTime('@0');
        $dtLaenge = new DateTime('@' . $objStats->getGesamtLaenge());
        $dtDuration = $dtLaenge->diff($dtNow);
        $this->Template->gesamtLaengeDateDiff = $dtDuration;
        $this->Template->gesamtLaenge = $objStats->getGesamtLaenge();

        $GLOBALS['TL_CSS']['minkorrekt'] = "bundles/minkorrekt/ribbons.css|1";

    }
}

class_alias(StatisticsContentElement::class, 'StatisticsContentElement');
