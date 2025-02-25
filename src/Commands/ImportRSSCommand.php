<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use lindesbs\minkorrekt\Constants\ThemenArt;
use lindesbs\minkorrekt\DCA\PodcastEpisode as DCAPodcastEpisode;
use lindesbs\minkorrekt\Entity\PodcastEpisode;
use lindesbs\minkorrekt\Entity\PodcastPassage;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;
use lindesbs\minkorrekt\Repository\PodcastKeywordsRepository;
use lindesbs\minkorrekt\Repository\PodcastPassageRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;

#[AsCommand(name: 'minkorrekt:importrss', description: 'Import RSS as Newslist')]
class ImportRSSCommand extends Command
{
    protected static $defaultURL = 'https://minkorrekt.de/feed/m4a/';

    private int $statusCode = Command::SUCCESS;

    public function __construct(
        private readonly ContaoFramework           $contaoFramework,
        private readonly PodcastEpisodeRepository  $podcastEntryRepository,
        private readonly PodcastPassageRepository  $podcastPassageRepository,
        private readonly PodcastKeywordsRepository $podcastKeywordsRepository,
        private readonly Connection                $connection,
        private readonly EntityManagerInterface    $entityManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        if ('dev' === $_SERVER['APP_ENV']) {
            $symfonyStyle->warning('DEV MODE');

            $platform = $this->connection->getDatabasePlatform();

            $this->connection->query('SET FOREIGN_KEY_CHECKS=0');
            $this->connection->executeQuery($platform->getTruncateTableSQL('mh_podcast_keywords', true));
            $this->connection->executeQuery($platform->getTruncateTableSQL('mh_podcast_episode', true));
            $this->connection->executeQuery($platform->getTruncateTableSQL('mh_podcast_passage', true));
            $this->connection->executeQuery($platform->getTruncateTableSQL('mh_join_podcast_keywords', true));

            $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
        }

        $symfonyStyle->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();

        $domDocument = new \DOMDocument();

        $symfonyStyle->writeln("Load RSS Feed");
        $strData = $this->getRSSFeed();
        $domDocument->loadXML($strData);
        $symfonyStyle->writeln("done");


        $domxPath = new \DOMXPath($domDocument);

//            $xp->registerNamespace('itunes','http://www.itunes.com/dtds/podcast-1.0.dtd');
//            $xp->registerNamespace('atom','http://www.w3.org/2005/Atom');

        /** @var \DOMNodeList $path */
        $path = $domxPath->query('//channel/item');

        $symfonyStyle->writeln(\count($path) . ' Elemente');

        foreach ($path as $element) {
            $entry = new DCAPodcastEpisode($element);

            /** @var PodcastEpisode $existingEpisode */
            $existingEpisode = $this->podcastEntryRepository->findOneBy(['episode' => (string)$entry->getEpisode()]);

            if (!$existingEpisode) {
                $existingEpisode = new PodcastEpisode();
            }

            $existingEpisode->setTitle($entry->getTitle());
            $existingEpisode->setSubtitle($entry->getSubtitle());
            $existingEpisode->setGuid($entry->getGuid() ?? null);

            $existingEpisode->setEpisode($entry->getEpisode());
            $existingEpisode->setDescription($entry->getDescription());

            $existingEpisode->setAuthor($entry->getAuthor() ?? null);
            $existingEpisode->setPubDate($entry->getPubDate() ?? null);
            $existingEpisode->setLink($entry->getLink() ?? null);

            $existingEpisode->setDuration($entry->getDuration() ?? null);
            $existingEpisode->addKeywords($entry->getKeywords() ?? [], $this->entityManager, $this->podcastKeywordsRepository);
            $existingEpisode->setExplicit($entry->isExplicit() ?? null);

            $existingEpisode->setSlug( System::getContainer()->get('contao.slug')->generate($existingEpisode->getTitle()));


            $workingData = explode("\n", $entry->getContent());
            $workingData = array_map('trim', $workingData);

            $sorting=1;

            foreach ($workingData as $key => $value) {
                if ('' === strip_tags(trim($value))) {
                    continue;
                }

                if (str_starts_with($value, '<!--')) {
                    continue;
                }

                $contentAlias = md5($value).'_'.$sorting;

                /** @var PodcastPassage $objContent */
                $objContent = $this->podcastPassageRepository->findOneBy([
                    'thema_alias'=>$contentAlias
                ]);

                if (!$objContent) {
                    $objContent = new PodcastPassage();
                    $objContent->setThemaAlias($contentAlias);
                }

                $objContent->setThemaArt(ThemenArt::TEXT);

                //$value = preg_replace(['/^<p>/', '/<\/p>$/'], '', $value);
                $objContent->setContent(trim($value));
                
                $objContent->setThemaNr(0);

                $pattern = '/^Thema\s+(\d+)/';

                if (preg_match($pattern, trim(strip_tags((string) $objContent->getContent())), $matches)) {
                    $objContent->setThemaArt(ThemenArt::THEMA);

                    $number = $matches[1];
                    if (is_numeric($number)) {
                        $objContent->setThemaNr((int) $number);
                    }
                }

                $existingEpisode->addPassage($objContent);

                $objContent->setSorting($sorting++);
                $objContent->setPaper(null);

                $this->entityManager->persist($objContent);
            }

            $this->entityManager->persist($existingEpisode);
            $this->entityManager->flush();
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
