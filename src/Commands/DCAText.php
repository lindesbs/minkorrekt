<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use lindesbs\ContaoTools\Class\DCA;
use lindesbs\ContaoTools\Class\DCAType;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;

class DCAText extends Command
{

    protected static $defaultName = 'minkorrekt:dca';
    protected static $defaultDescription = 'Import RSS as Newslist';


    protected function configure(): void
    {
        $this->setDescription('Gibt einen Demotext aus.');
    }
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);



        DCA::DCA('minkorrekt_thema_art',
            DCA::Group('minkorrekt', [
                DCA::Field('minkorrekt_thema_art', DCAType::SELECT),
                DCA::Field('minkorrekt_thema_folge', DCATYPE::TEXT),
                DCA::Field('minkorrekt_thema_nummer', DCAType::TEXT),
            ],
            )
        );

        return Command::SUCCESS;
    }

}