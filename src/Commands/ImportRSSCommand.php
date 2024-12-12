<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model;
use Contao\Model\Collection;
use Contao\NewsModel;
use Contao\StringUtil;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use lindesbs\minkorrekt\Constants\BearbeitungsStatus;
use lindesbs\minkorrekt\Constants\ThemenArt;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use lindesbs\minkorrekt\Models\MinkorrektThemenModel;
use lindesbs\toolbox\Service\DCATools;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;
use function count;


#[AsCommand(
    name: 'minkorrekt:importrss',
    description: 'Import RSS as Newslist',
)]
class ImportRSSCommand extends Command
{
    protected static $defaultURL = 'https://minkorrekt.de/feed/m4a/';

    private int $statusCode = Command::SUCCESS;

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly DCATools        $DCATools,
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
    protected function execute(InputInterface $input, OutputInterface $output): int
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

        /**
         * @var DOMNodeList $path
         */
        $path = $domxPath->query('//channel/item');

        $io->writeln(count($path) . ' Elemente');

        $prog = $io->createProgressBar();
        $prog->start(count($path));
        foreach ($path as $element) {
            $entry = new PodcastEntry($element);

            $alias = StringUtil::generateAlias(sprintf('Episode%s', $entry->getEpisode()));

            $objFolge = $this->getFolge($alias, $entry);
            $objFeed = $this->DCATools->getNews(
                sprintf('%s_F%s', $entry->getTitle(), $entry->getEpisode()),
                [
                    'date' => $entry->getPubDate(),
                    'teaser' => $entry->getDescription(),
                ],
                $objNewsArchive
            );

            if (empty($objFeed->minkorrekt_wip)) {
                $objFeed->minkorrekt_wip = BearbeitungsStatus::UNBEARBEITET;
            }

            if ($objFeed->minkorrekt_wip == BearbeitungsStatus::IN_BEARBEITEUNG) {
                continue;
            }

            if ($objFeed->minkorrekt_wip == BearbeitungsStatus::ABGENOMMEN) {
                continue;
            }


            $workingData = explode("\n", $entry->getContent());
            $workingData = array_map('trim', $workingData);

            $firstContent = true;

            foreach ($workingData as $key => $value) {
                if ('' === strip_tags(trim($value))) {
                    continue;
                }

                if (str_starts_with($value, '<!--')) {
                    continue;
                }

                $objContent = $this->createNewsContent($objFeed, $key, $value, $entry);

                if ($firstContent) {
                    $objContent->minkorrekt_thema_art = ThemenArt::BEGRUESSUNG;
                    $objContent->save();
                    $firstContent=false;
                }
            }

            $objFolge->newsId = $objFeed->id;

            $objFolge->duration = $entry->getDuration();
            $objFolge->pubdate = $entry->getPubDate();

            if ($entry->isEnclosure()) {
                $objFolge->save();
            } else {
                $objFolge->delete();
            }

            $prog->advance();
        }

        $prog->finish();

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

                $rssFeed = file_get_contents(self::$defaultURL);

                file_put_contents("rss_feed.txt", $rssFeed);
                return $rssFeed;
            }
        );
    }

    /**
     * @param string $alias
     * @param PodcastEntry $entry
     * @return Model|Model[]|Collection|MinkorrektFolgenModel|null
     */
    public function getFolge(
        string       $alias,
        PodcastEntry $entry
    ): MinkorrektFolgenModel|array|Model|null|Collection {
        $objFolge = MinkorrektFolgenModel::findByIdOrAlias($alias);

        if (!$objFolge) {
            $objFolge = new MinkorrektFolgenModel();
            $objFolge->tstamp = time();
            $objFolge->alias = $alias;
        }

        $objFolge->title = $entry->getTitle();
        $objFolge->content = $entry->getContent();
        $objFolge->episode = $entry->getEpisode();
        return $objFolge;
    }

    /**
     * @param string $contentAlias
     * @param NewsModel $objFeed
     * @param int|string $key
     * @param PodcastEntry $entry
     * @return mixed
     */
    public function createNewsContent(
        NewsModel    $objFeed,
        int|string   $key,
        mixed        $feedLine,
        PodcastEntry $entry
    ): ContentModel {
        $contentAlias = sprintf("%s_%s", $entry->getEpisode(), $key);

        $objContent = ContentModel::findByIdOrAlias($contentAlias);

        if (!$objContent) {
            $objContent = new ContentModel();
        }

        $objContent->alias = $contentAlias;
        $objContent->tstamp = 1;
        $objContent->pid = $objFeed->id;
        $objContent->sorting = $key;
        $objContent->ptable = 'tl_news';
        $objContent->type = 'minkorrekt_thema';

        $objContent->minkorrekt_thema_art = 'TEXT';
        $objContent->text = trim(strip_tags((string) $feedLine, '<a>'));
        $objContent->minkorrekt_thema_nummer = 0;

        $pattern = '/^Thema\s+(\d+)/';

        if (preg_match($pattern, trim(strip_tags($objContent->text)), $matches)) {
            $objContent->minkorrekt_thema_art = ThemenArt::THEMA;

            $number = $matches[1];
            $objContent->headline = [
                'value' => sprintf('Thema %s', $number),
                'unit' => 'h3',
            ];

            if (is_numeric($number)) {
                $objContent->minkorrekt_thema_nummer = $number;
            }

            $aliasThema = sprintf("F%sT%s", $entry->getEpisode(), $number);
            $objThema = MinkorrektThemenModel::findByIdOrAlias($aliasThema);
            if (!$objThema) {
                $objThema = new MinkorrektThemenModel();
                $objThema->alias = $aliasThema;
                $objThema->abgenommen = false;
                $objThema->tstamp = time();
            }

            $objThema->title = $entry->getTitle();

            $objThema->tstamp = time();
            $pattern = '@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@';

            $url = 'unknown';
            if (preg_match(
                $pattern, $objContent->text, $result
            )
            ) {
                $url = array_shift($result);
                $objThema->link = $url;
            }
            $objThema->save();
        }

        $suchstring = trim(strip_tags($objContent->text));

        $patternTimetable = '/^\d{2}:\d{2}:\d{2}/';
        if (preg_match($patternTimetable, $suchstring, $matches)) {
            $objContent->minkorrekt_thema_art = ThemenArt::TIMETABLE;
        } else {
            $this->setThemenArt('Snackable Science', ThemenArt::SNACKABLESCIENCE, $objContent);
            $this->setThemenArt('Kommentar', ThemenArt::KOMMENTAR, $objContent);
            $this->setThemenArt('Schwurbel', ThemenArt::SCHWURBEL, $objContent);
            $this->setThemenArt('Hausmeisterei', ThemenArt::HAUSMEISTEREI, $objContent);
            $this->setThemenArt('Gadget', ThemenArt::GADGET, $objContent);
            $this->setThemenArt('Experiment', ThemenArt::EXPERIMENT, $objContent);
        }

        $objContent->minkorrekt_thema_folge = $entry->getEpisode();
        $objContent->save();

        return $objContent;
    }

    /**
     * @param string $suchstring
     * @param array $matches
     * @param ContentModel|null $objContent
     * @return array
     */
    public function setThemenArt(string $strText, $themenArt, ?ContentModel $objContent): void
    {
        $suchstring = trim(strip_tags($objContent->text));

        $pattern= '/'.$strText.'/';

        $substring = substr($suchstring, 0, 30);
        if (preg_match($pattern, $substring, $matches)) {
            $objContent->minkorrekt_thema_art = $themenArt;
        }

    }

}
