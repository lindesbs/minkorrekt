<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\toolbox\Service\DCATools;
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
        private readonly DCATools $DCATools,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();

        $domDocument = new DOMDocument();

        $io->writeln("Load RSS Feed");
        $strData = $this->getRSSFeed();
        $domDocument->loadXML($strData);
        $io->writeln("done");

        $objNewsArchive = $this->DCATools->getNewsArchive('Methodisch Inkorrekt');

        $domxPath = new DOMXPath($domDocument);

//            $xp->registerNamespace('itunes','http://www.itunes.com/dtds/podcast-1.0.dtd');
//            $xp->registerNamespace('atom','http://www.w3.org/2005/Atom');

        /** @var DOMNodeList $path */
        $path = $domxPath->query('//channel/item');

        $io->writeln(count($path) . ' Elemente');

        foreach ($path as $element) {
            $entry = new PodcastEntry($element);

            $alias = StringUtil::generateAlias(sprintf('%s_F%s', $entry->getTitle(), $entry->getEpisode()));

            $objFolge = MinkorrektFolgenModel::findByIdOrAlias($alias);

            if (!$objFolge) {
                $objFolge = new MinkorrektFolgenModel();
                $objFolge->tstamp = time();
                $objFolge->alias = $alias;
            }

            $objFolge->title = $entry->getTitle();
            $objFolge->content = $entry->getContent();
            $objFolge->episode = $entry->getEpisode();

            // News generieren


            $objFeed = $this->DCATools->getNews(
                sprintf('%s_F%s', $entry->getTitle(), $entry->getEpisode()),
                [
                    'date' => $entry->getPubDate()->getTimestamp(),
                    'teaser' => $entry->getDescription(),
                ],
                $objNewsArchive
            );

            $workingData = explode("\n", $entry->getContent());

            $workingData = array_map('trim', $workingData);

            foreach ($workingData as $key => $value) {
                if ('' === strip_tags(trim($value))) {
                    continue;
                }

                if (str_starts_with($value, '<!--')) {
                    continue;
                }

                $contentAlias = md5($value);
                $objContent = ContentModel::findByArticleAlias($contentAlias);

                if (!$objContent) {
                    $objContent = new ContentModel();
                }

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

            $objFolge->newsId = $objFeed->id;
            $objFolge->save();
        }

        return $this->statusCode;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getRSSFeed(): string
    {
        $filesystemAdapter = new FilesystemAdapter();

        return $filesystemAdapter->get(
            'RSSFeed',
            static function (ItemInterface $item): string|bool {
                $item->expiresAfter(86400);

                return file_get_contents(self::$defaultURL);
            }
        );
    }
}
