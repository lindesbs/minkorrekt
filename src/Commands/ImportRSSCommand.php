<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\NewsArchiveModel;
use Contao\NewsFeedModel;
use Contao\NewsletterModel;
use Contao\NewsModel;
use Laminas\Feed\Reader\Reader;
use Laminas\Feed\Reader\Reader as FeedReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;


class ImportRSSCommand extends Command
{
    protected static $defaultName = 'minkorrekt:importrss';
    protected static $defaultDescription = 'Import RSS as Newslist';

    protected static $defaultURL = "https://minkorrekt.de/feed/m4a/";

    private Filesystem $filesystem;
    private int $statusCode = Command::SUCCESS;

    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $this->framework = $framework;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Minkorrekt RSS einlesen und importieren');


        $this->framework->initialize();

        $feed = Reader::import(self::$defaultURL);

        $data = [
            'title' => $feed->getTitle(),
            'link' => $feed->getLink(),
            'dateModified' => $feed->getDateModified(),
            'description' => $feed->getDescription(),
            'language' => $feed->getLanguage(),
            'entries' => [],
        ];


        $objNewsCatalog = NewsArchiveModel::findByTitle($feed->getTitle());
        if (!$objNewsCatalog) {
            $objNewsCatalog = new NewsArchiveModel();
            $objNewsCatalog->tstamp = time();

            $objNewsCatalog->title = $feed->getTitle();
        }

        $objNewsCatalog->save();


        foreach ($feed as $entry) {

            $objFeed = NewsModel::findByAlias($entry->getId());

            if (!$objFeed) {
                $objFeed = new NewsModel();
                $objFeed->tstamp = time();
                $objFeed->pid = $objNewsCatalog->id;

                $objFeed->alias = $entry->getId();
            }
            $objFeed->headline = $entry->getTitle();

            $objFeed->date = $entry->getDateModified()->getTimestamp();
            $objFeed->teaser = $entry->getDescription();

            $objFeed->published = true;

            $objFeed->save();



            $objContent = ContentModel::findBy(['pid', 'ptable', 'tstamp'],[$objFeed->id, 'tl_news', 1]);

            if (!$objContent) {
                $objContent = new ContentModel();
                $objContent->tstamp = 1;
                $objContent->pid = $objFeed->id;
                $objContent->ptable = 'tl_news';
                $objContent->type = 'text';
            }

            $objContent->text = $entry->getContent();

            $objContent->save();

            $edata = [
                'title' => $entry->getTitle(),
                'description' => $entry->getDescription(),
                'dateModified' => $entry->getDateModified(),
                'authors' => $entry->getAuthors(),
                'link' => $entry->getLink(),
                'content' => $entry->getContent(),
            ];

            $data['entries'][] = $edata;
        }

        return $this->statusCode;
    }
}