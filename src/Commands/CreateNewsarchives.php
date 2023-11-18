<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;
use lindesbs\minkorrekt\Models\MinkorrektThemenModel;
use lindesbs\minkorrekt\Service\WebsiteScraperPaper;
use lindesbs\minkorrekt\Service\WebsiteScraperPublisher;
use lindesbs\toolbox\Service\DCATools;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateNewsarchives extends Command
{
    protected static $defaultName = 'minkorrekt:newsarchives';

    protected static $defaultDescription = 'Fetch websites and crawl them';

    public function __construct(
        private readonly ContaoFramework     $contaoFramework,
        private readonly Connection          $connection,
        private readonly DCATools            $DCATools,
        private readonly WebsiteScraperPaper $paperScraper,
        private readonly WebsiteScraperPublisher $publisherScraper,
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        $newsPublisher = $this->DCATools->getNewsArchive('Publisher');

        $arrPublisher=[];
        $objThemen = MinkorrektThemenModel::findAll();

        if ($objThemen) {
            $prog = $io->createProgressBar($objThemen->count());
            $prog->start();
            foreach ($objThemen as $thema) {
                if (!($thema->abgenommen) || (!isset($thema->link))) {
                    continue;
                }

                $objPaper = MinkorrektPaperModel::findByIdOrAlias($thema->alias);
                if (!$objPaper) {
                    $objPaper = new MinkorrektPaperModel();
                    $objPaper->alias = $thema->alias;
                    $objPaper->tstamp = time();
                }

                $objPaper->url = $thema->link;

                $paper = $this->paperScraper->scrape($objPaper);
                $objPaper->save();

                if ($paper->url)
                {
                    $theUrl = parse_url($paper->url);
                    if (array_key_exists('host',$theUrl)) {
                        $publisher = $this->getPublisher(StringUtil::generateAlias($theUrl['host']));

                        $publisher->url = $theUrl['host'];

                        $this->publisherScraper->scrape($publisher);

                        $publisher->save();
                    }
                }


                $prog->advance();
            }

            $prog->finish();
        }

        file_put_contents("missingMeta-paper.json", Json::encode($this->paperScraper->getUnknownMeta()));
        file_put_contents("missingMeta-publisher.json", Json::encode($this->publisherScraper->getUnknownMeta()));
        return Command::SUCCESS;
    }


    public function getPublisher(
        string       $alias
    ): MinkorrektPublisherModel|array|Model|null|Collection
    {
        $Publisher = MinkorrektPublisherModel::findByIdOrAlias($alias);

        if (!$Publisher) {
            $Publisher = new MinkorrektPublisherModel();
            $Publisher->tstamp = time();
            $Publisher->alias = $alias;
        }

        return $Publisher;
    }

}
