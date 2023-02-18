<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\src\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use lindesbs\minkorrekt\Service\DCATools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class buildSystemStructure extends Command
{
    protected static $defaultName = 'minkorrekt:buildSystem';

    protected static $defaultDescription = 'Seiten, Module und sonstiges erstellen';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly DCATools $DCATools
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $publisherDetailPage = null;
        $paperDetailPage = null;
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();


        $rootPage = PageModel::findByIdOrAlias('minkorrekt-history');

        if ($rootPage) {
            $sciencePage = $this->DCATools->getPage("Wissenschaft", $rootPage->id);

            $publisherPage = $this->DCATools->getPage("Verlag", $sciencePage->id);
            $publisherDetailPage = $this->DCATools->getPage("Verlagsinformationen", $publisherPage->id);
            $publisherDetailPage->hide = true;
            $publisherDetailPage->save();

            $paperPage = $this->DCATools->getPage("Paper", $sciencePage->id);
            $paperDetailPage = $this->DCATools->getPage("Paper Details", $paperPage->id);
            $paperDetailPage->hide = true;
            $paperDetailPage->save();

            $statisticsPage = $this->DCATools->getPage("Statistiken", $sciencePage->id);


            $newsPublisher = $this->DCATools->getNewsArchive("Publisher");
            $newsPublisher->jumpTo = $publisherDetailPage->id;
            $newsPublisher->save();


            $newsPaper = $this->DCATools->getNewsArchive("Paper");
            $newsPaper->jumpTo = $paperDetailPage->id;
            $newsPaper->save();
        }
    }
}
