<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use DOMDocument;
use DOMXPath;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportRSSCommand extends Command
{
    protected static $defaultName = 'minkorrekt:importrss';

    protected static $defaultDescription = 'Import RSS as Newslist';

    protected static $defaultURL = "https://minkorrekt.de/feed/m4a/";

    private int $statusCode = Command::SUCCESS;

    public function __construct(
        private readonly ContaoFramework $contaoFramework
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
        $symfonyStyle->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();

        $domDocument = new DOMDocument();
        $strData = file_get_contents(self::$defaultURL);
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

            $pregMatch = preg_match_all('#<!-- wp:paragraph -->(.*?)<!-- \/wp:paragraph -->#s', $entry->getContent(), $match);
            $workingData = $pregMatch ? $match[1] : explode("\n", $entry->getContent());


            foreach ($workingData as $key => $value) {
                if (strlen(strip_tags(trim((string)$value))) == 0)
                    continue;

                $objContent = new ContentModel();
                $objContent->tstamp = 1;
                $objContent->pid = $objFeed->id;
                $objContent->sorting = $key;
                $objContent->ptable = 'tl_news';
                $objContent->type = 'minkorrekt_thema';

                $objContent->minkorrekt_thema_art = "TEXT";
                $objContent->text = trim((string)$value);

                if (($matchThema) && ($matchThema[0][1] < 100)) {

                    $objContent->minkorrekt_thema_art = "THEMA";

                    $objContent->headline = [
                        'value' => $matchThema[0][0],
                        'unit' => 'h3'
                    ];


                }

                $objContent->minkorrekt_thema_folge = $entry->getEpisode();


                $objContent->save();

            }


        }


        return $this->statusCode;
    }
}