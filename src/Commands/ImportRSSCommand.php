<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use lindesbs\minkorrekt\DCA\PodcastEpisode as DCAPodcastEpisode;
use lindesbs\minkorrekt\Entity\PodcastEpisode;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;
use lindesbs\minkorrekt\Repository\PodcastKeywordsRepository;
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
        private readonly PodcastKeywordsRepository $podcastKeywordsRepository,

        private readonly EntityManagerInterface    $entityManager
    )
    {
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

            //  $platform = $this->connection->getDatabasePlatform();
            //   $this->connection->executeUpdate($platform->getTruncateTableSQL('podcast_episode', true));
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

            $this->entityManager->persist($existingEpisode);
        }

        $this->entityManager->flush();

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
