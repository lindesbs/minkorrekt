<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;
use lindesbs\minkorrekt\Service\WebsiteScraper;
use lindesbs\toolbox\Service\DCATools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateNewsarchives extends Command
{
    protected static $defaultName = 'minkorrekt:newsarchives';

    protected static $defaultDescription = 'Fetch websites and crawl them';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly Connection $connection,
        private readonly DCATools $DCATools,
        private readonly WebsiteScraper $scraper,
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

        $this->rebuild($io);

        $newsPublisher = $this->DCATools->getNewsArchive('Publisher');
        $newsPaper = $this->DCATools->getNewsArchive('Paper');

        $objPapera = MinkorrektPaperModel::findAll();

        if ($objPapera) {
            $prog = $io->createProgressBar($objPapera->count());
            $prog->start();
            foreach ($objPapera as $paper) {
                if (!isset($paper->url)) {
                    continue;
                }

                $arrOptions = [
                    'date' => (int)$paper->publishedAt,
                ];

                $objNews = $this->DCATools->getNews(
                    $paper->title,
                    $arrOptions,
                    $newsPaper
                );

                $objContent = $this->DCATools->getContent($paper->title, [], $objNews, true);
                $objContent->text = $paper->description;
                $objContent->addImage = true;
                $objContent->singleSRC = $paper->screenshotSRC;
                $objContent->ptable = 'tl_news';

                $objContent->save();

                $prog->advance();
//            $this->scraper->scrape($paper);
            }

            $prog->finish();
        }

        $this->rebuild($io);

        return Command::SUCCESS;
    }

    public function rebuild(SymfonyStyle $io): void
    {
//        if ($_SERVER['APP_ENV'] === 'dev') {
//            $this->Database->execute("TRUNCATE TABLE tl_minkorrekt_paper");
//        }

        $sql = "SELECT * FROM tl_content WHERE ptable='tl_news' AND minkorrekt_thema_art='THEMA'";
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $io->writeln("Create Paper & Publisher entries");


        $prog = $io->createProgressBar();
        $prog->start(count($result));

        foreach ($result as $data) {
            $aliasPaper = sprintf('F%sT%s', $data['minkorrekt_thema_folge'], $data['minkorrekt_thema_nummer']);

            $pattern = '/(https?|ftp):\/\/[^\s\/$.?#].[^\s]*/i';
            $url = 'unknown';
            if (preg_match('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $data['text'], $result)) {
                $url = array_shift($result);

                $decodedUrl = parse_url((string)$url);

                $alias = StringUtil::generateAlias($decodedUrl['host']);
                $publisher = MinkorrektPublisherModel::findByIdOrAlias($alias);

                if (!$publisher) {
                    $publisher = new MinkorrektPublisherModel();

                    $publisher->tstamp = time();
                    $publisher->alias = $alias;

                    $publisher->url = sprintf("%s://%s/", $decodedUrl['scheme'], $decodedUrl['host']);
                    $publisher->title = $decodedUrl['host'];

                    $publisher->save();
                }
            }

            $objPaper = MinkorrektPaperModel::findByIdOrAlias($aliasPaper);

            if (!$objPaper) {
                $objPaper = new MinkorrektPaperModel();

                $objPaper->tstamp = time();
                $objPaper->alias = $aliasPaper;
            }
            $objPaper->published = false;
            $objPaper->tlContentId = $data['id'];
            $objPaper->tlNewsId = $data['pid'];
            $objPaper->url = trim($url, "'\"");

            if ($objPaper->url) {
                System::getContainer()->get('lindesbs.minkorrekt.websitescrape')->scrape($objPaper);
            }

            $prog->advance();

            $objPaper->save();
        }

        $prog->finish();
    }
}
