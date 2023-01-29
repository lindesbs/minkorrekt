<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use lindesbs\ContaoTools\Classes\DCA;
use lindesbs\ContaoTools\Classes\DCAType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DCAText extends Command
{
    protected static $defaultName = 'minkorrekt:dca';
    protected static $defaultDescription = 'DCA Testing';

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $arrv = DCA::DCA('minkorrekt_thema_art', false,
            DCA::Group('minkorrekt', [
                DCA::Field('minkorrekt_thema_art', DCAType::SELECT),
                DCA::Field('minkorrekt_thema_folge', DCATYPE::TEXT),
                DCA::Field('minkorrekt_thema_nummer', DCAType::TEXT),
            ],
            )
        );

        dump($arrv);

        return Command::SUCCESS;
    }

}