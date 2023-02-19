<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\contaotoolbox\Service\DCATools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateNewsarchives extends Command
{
    protected static $defaultName = 'minkorrekt:newsarchives';

    protected static $defaultDescription = 'Create screenhots of websites';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly DCATools $DCATools
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        $newsPublisher = $this->DCATools->getNewsArchive("Publisher");
        $newsPaper = $this->DCATools->getNewsArchive("Paper");

        return Command::SUCCESS;
    }
}
