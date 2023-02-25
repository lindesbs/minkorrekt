<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Exception;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
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

        $newsPublisher = $this->DCATools->getNewsArchive('Publisher');
        $newsPaper = $this->DCATools->getNewsArchive('Paper');

        $objPapera = MinkorrektPaperModel::findAll();

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
//            $this->scraper->scrape($paper);
        }

        return Command::SUCCESS;
    }
}
