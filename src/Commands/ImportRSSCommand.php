<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\NewsModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use lindesbs\contaotoolbox\Service\DCATools;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;

use function count;

class ImportRSSCommand extends Command
{
    protected static $defaultName = 'minkorrekt:importrss';

    protected static $defaultDescription = 'Import RSS as Newslist';

    protected static $defaultURL = 'https://minkorrekt.de/feed/m4a/';

    private int $statusCode = Command::SUCCESS;

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly Connection $connection,
        private readonly DCATools $DCATools
    ) {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        if ('dev' === $_SERVER['APP_ENV']) {
            $symfonyStyle->warning('DEV MODE');
            $this->connection->executeQuery('TRUNCATE TABLE tl_news');
            $this->connection->executeQuery('DELETE FROM tl_content WHERE ptable="tl_news"');
        }

        $symfonyStyle->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();

        $domDocument = new DOMDocument();

        $strData = $this->getRSSFeed();

        $domDocument->loadXML($strData);
        $objNewsArchive = $this->DCATools->getNewsArchive('Methodisch Inkorrekt');

        $domxPath = new DOMXPath($domDocument);

//            $xp->registerNamespace('itunes','http://www.itunes.com/dtds/podcast-1.0.dtd');
//            $xp->registerNamespace('atom','http://www.w3.org/2005/Atom');

        /** @var DOMNodeList $path */
        $path = $domxPath->query('//channel/item');

        $symfonyStyle->writeln(count($path) . ' Elemete');

        foreach ($path as $element) {
            $entry = new PodcastEntry($element);


            $objFeed = $this->DCATools->getNews(
                sprintf("%s_F%s", $entry->getTitle(), $entry->getEpisode()),
                [
                    'date' => $entry->getPubDate()->getTimestamp(),
                    'teaser' => $entry->getDescription()
                    ],
                $objNewsArchive
            );


           // $objFeed = NewsModel::findByAlias($entry->getGuid());
//
//            if (null !== $objFeed) {
//                continue;
//            }
//
//            $objFeed = new NewsModel();
//            $objFeed->tstamp = time();
//            $objFeed->pid = $objNewsArchive->id;
//
//            $objFeed->alias = StringUtil::generateAlias(sprintf("%s_F%s", $entry->getTitle(), $entry->getEpisode()));
//
//            $objFeed->headline = $entry->getTitle();
//
//            $objFeed->date = $entry->getPubDate()->getTimestamp();
//            $objFeed->teaser = $entry->getDescription();
//
//            $objFeed->published = true;
//            $objFeed->save();

            $workingData = explode("\n", $entry->getContent());
            $workingData = array_map('trim', $workingData);

            foreach ($workingData as $key => $value) {
                if ('' === strip_tags(trim($value))) {
                    continue;
                }

                if (str_starts_with($value, '<!--')) {
                    continue;
                }

                $objContent = new ContentModel();
                $objContent->tstamp = 1;
                $objContent->pid = $objFeed->id;
                $objContent->sorting = $key;
                $objContent->ptable = 'tl_news';
                $objContent->type = 'minkorrekt_thema';

                $objContent->minkorrekt_thema_art = 'TEXT';
                $objContent->text = trim($value);
                $objContent->minkorrekt_thema_nummer = 0;

                $pattern = '/^Thema\s+(\d+)/';

                if (preg_match($pattern, trim(strip_tags($objContent->text)), $matches)) {
                    $objContent->minkorrekt_thema_art = 'THEMA';

                    $number = $matches[1];
                    $objContent->headline = [
                        'value' => sprintf('Thema %s', $number),
                        'unit' => 'h3',
                    ];

                    if (is_numeric($number)) {
                        $objContent->minkorrekt_thema_nummer = $number;
                    }
                }

                $objContent->minkorrekt_thema_folge = $entry->getEpisode();
                $objContent->save();
            }
        }

        return $this->statusCode;
    }


    /**
     * @throws InvalidArgumentException
     */
    public function getRSSFeed(): string
    {
        $filesystemAdapter = new FilesystemAdapter();

        $strData = $filesystemAdapter->get(
            'RSSFeed',
            static function (ItemInterface $item): string|bool {
                $item->expiresAfter(86400);

                return file_get_contents(self::$defaultURL);
            }
        );
        return $strData;
    }

}
