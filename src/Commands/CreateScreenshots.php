<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Dbafs;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use lindesbs\minkorrekt\src\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\src\Models\MinkorrektPublisherModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    /**
     * @throws \Exception
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

    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        $filesystem = new Filesystem();

        $objSQLPublisher = $this->connection->executeQuery(
            'SELECT * FROM tl_minkorrekt_publisher WHERE url IS NOT NULL'
        );
        $objPublisher = $objSQLPublisher->fetchAllAssociative();

        foreach ($objPublisher as $publisher) {
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
        }

        $objSQLPaper = $this->connection->executeQuery('SELECT * FROM tl_minkorrekt_paper WHERE url IS NOT NULL');
        $objPaper = $objSQLPaper->fetchAllAssociative();

        foreach ($objPaper as $paper) {
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
        }

        return Command::SUCCESS;
    }
}
