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
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[\Symfony\Component\Console\Attribute\AsCommand('minkorrekt:newsarchives', 'Fetch websites and crawl them')]
class CreateNewsarchives extends Command
{
    public function __construct(
        private readonly WebsiteScraperPaper $paperScraper,
        private readonly WebsiteScraperPublisher $publisherScraper,
        private readonly ContaoFramework $contaoFramework
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $this->contaoFramework->initialize();
        $io = new SymfonyStyle($input, $output);
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

                if ($paper->url) {
                    $theUrl = parse_url((string) $paper->url);
                    if (array_key_exists('host', $theUrl)) {
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
    ): MinkorrektPublisherModel|array|Model|null|Collection {
        $Publisher = MinkorrektPublisherModel::findByIdOrAlias($alias);

        if (!$Publisher) {
            $Publisher = new MinkorrektPublisherModel();
            $Publisher->tstamp = time();
            $Publisher->alias = $alias;
        }

        return $Publisher;
    }

}
