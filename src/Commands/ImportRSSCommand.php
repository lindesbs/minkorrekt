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
use lindesbs\minkorrekt\Models\MinkorrektFolgenInhaltModel;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;
use function count;
#[\Symfony\Component\Console\Attribute\AsCommand('minkorrekt:importrss', 'Import RSS as Newslist')]
class ImportRSSCommand extends Command
{
    protected static $defaultDescription = 'Aus dem Minkorrekt RSS Feed an notwendigen Infos herausziehen';
    protected static $defaultURL = 'https://minkorrekt.de/feed/m4a/';

    private int $statusCode = Command::SUCCESS;


    public function __construct(
        private readonly ContaoFramework $contaoFramework,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $this->contaoFramework->initialize();
        $io = new SymfonyStyle($input, $output);

        $io->title('Minkorrekt RSS einlesen und importieren');

        $domDocument = new DOMDocument();

        $io->writeln("Load RSS Feed");
        $strData = $this->getRSSFeed();
        $domDocument->loadXML($strData);
        $io->writeln("done");

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

            if (empty($objFolge->getWip())) {
                $objFolge->setWip(BearbeitungsStatus::UNBEARBEITET);
            }

            if ($objFolge->getWip() == BearbeitungsStatus::IN_BEARBEITEUNG) {
                continue;
            }

            if ($objFolge->getWip() == BearbeitungsStatus::ABGENOMMEN) {
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

                $objContent = $this->createNewsContent($objFolge, $key, $value, $entry);

                if ($firstContent) {
                    $objContent->thema_art = ThemenArt::BEGRUESSUNG;
                    $objContent->save();
                    $firstContent=false;
                }
            }

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
     * @return Model|Model[]|Collection|MinkorrektFolgenModel|null
     */
    public function getFolge(
        string       $alias,
        PodcastEntry $entry
    ): MinkorrektFolgenModel|array|Model|null|Collection {
        $objFolge = MinkorrektFolgenModel::findByIdOrAlias($alias);

        if (!$objFolge) {
            $objFolge = new MinkorrektFolgenModel();
            $objFolge->alias = $alias;
            $objFolge->save();
        }

        $objFolge->title = $entry->getTitle();
        $objFolge->content = $entry->getContent();
        $objFolge->episode = $entry->getEpisode();
        $objFolge->tstamp = $entry->getEpisode();
        return $objFolge;
    }

    /**
     * @param string $contentAlias
     * @return mixed
     */
    public function createNewsContent(
        MinkorrektFolgenModel   $objFolge,
        int|string   $key,
        mixed        $feedLine,
        PodcastEntry $entry
    ): MinkorrektFolgenInhaltModel {
        $contentAlias = sprintf("%s_%s", $entry->getEpisode(), $key);

        $objContent = MinkorrektFolgenInhaltModel::findByIdOrAlias($contentAlias);

        if (!$objContent) {
            $objContent = new MinkorrektFolgenInhaltModel();
        }

        $objContent->alias = $contentAlias;
        $objContent->sorting = $key;
        $objContent->pid = $objFolge->id;


        $objContent->thema_art = ThemenArt::TEXT;
        $objContent->text = trim(strip_tags((string) $feedLine, '<a>'));

        $pattern = '/^Thema\s+(\d+)/';

        if (preg_match($pattern, trim(strip_tags($objContent->text)), $matches)) {
            $objContent->thema_art = ThemenArt::THEMA;
            $number = $matches[1];
            $objContent->thema_nr = $number;

            $pattern = '@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@';

            $url = 'unknown';
            if (preg_match(
                $pattern, $objContent->text, $result
            )
            ) {
                $url = array_shift($result);
                $objContent->link = $url;
            }
        }

        $suchstring = trim(strip_tags($objContent->text));

        $patternTimetable = '/^\d{2}:\d{2}:\d{2}/';
        if (preg_match($patternTimetable, $suchstring, $matches)) {
            $objContent->thema_art = ThemenArt::TIMETABLE;
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
     * @return array
     */
    public function setThemenArt(string $strText, $themenArt, MinkorrektFolgenInhaltModel $objContent): void
    {
        $suchstring = trim(strip_tags((string) $objContent->text));

        $pattern= '/'.$strText.'/';

        $substring = substr($suchstring, 0, 30);
        if (preg_match($pattern, $substring, $matches)) {
            $objContent->thema_art = $themenArt;
        }

    }

}
