<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Doctrine\DBAL\Connection;
use DOMDocument;
use DOMXPath;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;

class ImportRSSCommand extends Command
{
    protected static $defaultName = 'minkorrekt:importrss';

    protected static $defaultDescription = 'Import RSS as Newslist';

    protected static $defaultURL = "https://minkorrekt.de/feed/m4a/";

    private int $statusCode = Command::SUCCESS;

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly Connection      $connection
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        if ($_SERVER['APP_ENV'] === 'dev') {
            $symfonyStyle->warning("DEV MODE");
            $this->connection->executeQuery("TRUNCATE TABLE tl_news");
            $this->connection->executeQuery('DELETE FROM tl_content WHERE ptable="tl_news"');
        }

        $symfonyStyle->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();


        $cache = new FilesystemAdapter();
        $domDocument = new DOMDocument();

        $strData = $cache->get('RSSFeed', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return file_get_contents(self::$defaultURL);
        });

        $domDocument->loadXML($strData);

        $title = "Methodisch Inkorrekt";

        $objNewsArchive = NewsArchiveModel::findByTitle($title);
        if ($objNewsArchive === null) {
            $objNewsArchive = new NewsArchiveModel();
            $objNewsArchive->tstamp = time();

            $objNewsArchive->title = $title;
        }

        $objNewsArchive->save();

        $domxPath = new DOMXPath($domDocument);

//            $xp->registerNamespace('itunes','http://www.itunes.com/dtds/podcast-1.0.dtd');
//            $xp->registerNamespace('atom','http://www.w3.org/2005/Atom');


        /** @var \DOMNodeList $path */
        $path = $domxPath->query("//channel/item");

        $symfonyStyle->writeln(count($path) . " Elemete");
        foreach ($path as $element) {
            $entry = new PodcastEntry($element);

            $objFeed = NewsModel::findByAlias($entry->getGuid());

            if ($objFeed !== null) {
                continue;
            }


            $objFeed = new NewsModel();
            $objFeed->tstamp = time();
            $objFeed->pid = $objNewsArchive->id;

            $objFeed->alias = $entry->getGuid();

            $objFeed->headline = $entry->getTitle();

            $objFeed->date = $entry->getPubDate()->getTimestamp();
            $objFeed->teaser = $entry->getDescription();

            $objFeed->published = true;
            $objFeed->save();

            $workingData = explode("\n", $entry->getContent());
            $workingData = array_map('trim', $workingData);

            foreach ($workingData as $key => $value) {
                if (strlen(strip_tags(trim((string)$value))) == 0) {
                    continue;
                }

                if (str_starts_with($value, "<!--")) {
                    continue;
                }


                $objContent = new ContentModel();
                $objContent->tstamp = 1;
                $objContent->pid = $objFeed->id;
                $objContent->sorting = $key;
                $objContent->ptable = 'tl_news';
                $objContent->type = 'minkorrekt_thema';

                $objContent->minkorrekt_thema_art = "TEXT";
                $objContent->text = trim((string)$value);
                $objContent->minkorrekt_thema_nummer = 0;

                $pattern = '/^Thema\s+(\d+)/';
                if (preg_match($pattern, trim(strip_tags($objContent->text)), $matches)) {
                    $objContent->minkorrekt_thema_art = "THEMA";

                    $number = $matches[1];
                    $objContent->headline = [
                        'value' => sprintf("Thema %s", $number),
                        'unit' => 'h3'
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
}