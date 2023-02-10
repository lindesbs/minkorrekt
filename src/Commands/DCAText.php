<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\DCAToolTime\DCAToolTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DCAText extends Command
{
    protected static $defaultName = 'minkorrekt:dca';

    protected static $defaultDescription = 'DCA Testing';

    public function __construct(private readonly ContaoFramework $contaoFramework)
    {
        $this->contaoFramework->initialize();

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        new SymfonyStyle($input, $output);

        $dcaToolTime = new DCAToolTime();
        $treeBuilder = $dcaToolTime->getConfigTreeBuilder();


        dump($treeBuilder->buildTree());


//
//        $arrv = DCA::DCA('minkorrekt_thema_art', false,
//            DCA::Group('minkorrekt', [
//                DCA::Field('minkorrekt_thema_art', DCAType::SELECT),
//                DCA::Field('minkorrekt_thema_folge', DCATYPE::TEXT),
//                DCA::Field('minkorrekt_thema_nummer', DCAType::TEXT),
//            ],
//            )
//        );
//
//        Controller::loadDataContainer('tl_content');
//        dump(DCA::flattenArray($GLOBALS['TL_DCA']['tl_content']));

        return Command::SUCCESS;
    }

}