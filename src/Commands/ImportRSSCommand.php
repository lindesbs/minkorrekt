<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Connection;
use lindesbs\minkorrekt\Classes\PodcastEntry;
use lindesbs\toolbox\Service\DCATools;
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
        private readonly ContaoFramework $contaoFramework,
        private readonly Connection $connection,
        private readonly DCATools $DCATools,
    ) {
        parent::__construct();
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
            $this->connection->executeQuery('TRUNCATE TABLE tl_news');
            $this->connection->executeQuery('DELETE FROM tl_content WHERE ptable="tl_news"');
        }

        $symfonyStyle->title('Minkorrekt RSS einlesen und importieren');

        $this->contaoFramework->initialize();

        $domDocument = new \DOMDocument();

        $symfonyStyle->writeln("Load RSS Feed");
        $strData = $this->getRSSFeed();
        $domDocument->loadXML($strData);
        $symfonyStyle->writeln("done");

        $objNewsArchive = $this->DCATools->getNewsArchive('Methodisch Inkorrekt');

        $domxPath = new \DOMXPath($domDocument);

//            $xp->registerNamespace('itunes','http://www.itunes.com/dtds/podcast-1.0.dtd');
//            $xp->registerNamespace('atom','http://www.w3.org/2005/Atom');

        /** @var \DOMNodeList $path */
        $path = $domxPath->query('//channel/item');

        $symfonyStyle->writeln(\count($path) . ' Elemente');

        foreach ($path as $element) {
            $entry = new PodcastEntry($element);

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
        }

        return $this->statusCode;
    }
}
