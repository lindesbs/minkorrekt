<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Dbafs;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Exception;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class CreateScreenshots extends Command
{
    protected static $defaultName = 'minkorrekt:screenshots';

    protected static $defaultDescription = 'Create screenhots of websites';

    protected static $thumbnailCommand = '/Applications/Google\\ Chrome.app/Contents/MacOS/Google\\ Chrome --headless --disable-gpu --hide-scrollbars --screenshot=##outputname## --window-size=1280,1060 ##url##';
    protected static $fullpageCommand = '/Applications/Google\\ Chrome.app/Contents/MacOS/Google\\ Chrome --headless --disable-gpu --hide-scrollbars --screenshot=##outputname## --window-size=1280,10600  ##url##';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption("force",
            null,
            InputOption::VALUE_NONE,
            "Force halt");
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        $bForce = $input->getOption("force");

        $filesystem = new Filesystem();

        $objSQLPublisher = $this->connection->executeQuery(
            'SELECT * FROM tl_minkorrekt_publisher WHERE url IS NOT NULL'
        );
        $objPublisher = $objSQLPublisher->fetchAllAssociative();

        $io->writeln("Publisher");
        $io->progressStart(count($objPublisher));
        foreach ($objPublisher as $publisher) {
            if (isset($publisher['screenshotSRC']) && (!$bForce)) {
                continue;
            }

            $io->writeln("+++");
            $destPath = sprintf(
                'files/media/paper/%s/',
                StringUtil::generateAlias($publisher['title'])
            );

            $filesystem->mkdir($destPath);
            $destinationVar = 'screenshotSRC';
            $filename = $this->makeScreenshot($destPath, $publisher, self::$thumbnailCommand, $destinationVar);

            $objDBAfsFile = Dbafs::addResource($filename);
            $objModel = MinkorrektPublisherModel::findByIdOrAlias($publisher['id']);
            $objModel->$destinationVar = $objDBAfsFile->uuid;
            $objModel->save();

            $destinationVar = 'screenshotFullpageSRC';
            $this->makeScreenshot($destPath, $publisher, self::$fullpageCommand, $destinationVar);

            $objDBAfsFile = Dbafs::addResource($filename);
            $objModel = MinkorrektPublisherModel::findByIdOrAlias($publisher['id']);
            $objModel->$destinationVar = $objDBAfsFile->uuid;
            $objModel->save();

            $io->progressAdvance();
        }

        $io->progressFinish();


        $objSQLPaper = $this->connection->executeQuery(
            'SELECT * FROM tl_minkorrekt_paper WHERE url IS NOT NULL'
        );
        $objPaper = $objSQLPaper->fetchAllAssociative();

        $io->writeln("Publisher");
        $io->progressStart(count($objPaper));

        foreach ($objPaper as $paper) {
            if (isset($paper['screenshotSRC']) && (!$bForce)) {
                continue;
            }

            $destPath = sprintf(
                'files/media/paper/%s/',
                StringUtil::generateAlias($paper['title'])
            );

            $filesystem->mkdir($destPath);
            $destinationVar = 'screenshotSRC';
            $filename = $this->makeScreenshot($destPath, $paper, self::$thumbnailCommand, $destinationVar);

            $objDBAfsFile = Dbafs::addResource($filename);
            $objModel = MinkorrektPaperModel::findByIdOrAlias($paper['id']);
            $objModel->$destinationVar = $objDBAfsFile->uuid;
            $objModel->save();

            $destinationVar = 'screenshotFullpageSRC';
            $filename = $this->makeScreenshot($destPath, $paper, self::$fullpageCommand, $destinationVar);

            $objDBAfsFile = Dbafs::addResource($filename);
            $objModel = MinkorrektPaperModel::findByIdOrAlias($paper['id']);
            $objModel->$destinationVar = $objDBAfsFile->uuid;
            $objModel->save();
            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function makeScreenshot(
        string $destPath,
        array $paper,
        string $captureCommand,
        string $destinationVar
    ): string {
        $filename = sprintf(
            '%s%s_%s_%s.png',
            $destPath,
            date('Ymd'),
            StringUtil::generateAlias($paper['title']),
            $destinationVar
        );

        $cmd = str_replace(
            ['##outputname##', '##url##'],
            [$filename, $paper['url']],
            $captureCommand
        );

        file_put_contents('runner.sh', $cmd);
        $process = new Process(['./runner.sh']);
        $process->run();

        return $filename;
    }
}
