<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\minkorrekt\Service\DCATools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class buildSystemStructure extends Command
{
    protected static $defaultName = 'minkorrekt:buildSystem';

    protected static $defaultDescription = 'Seiten, Module und sonstiges erstellen';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly DCATools $DCATools
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        $publisherDetailPage = null;
        $paperDetailPage = null;
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        $theme = $this->DCATools->getTheme("Standard");

        $modMainMenu = $this->DCATools->getModule(
            'Menu :: Main Top',
            [
                'type' => 'navigation',
                'navigationTpl' => 'nav_default_bootstrap_header',
                'pid' => $theme->id
            ]
        );

        $modFooterHTML = $this->DCATools->getModule(
            'Footer :: HTML',
            [
                'type' => 'html',
                'pid' => $theme->id
            ]
        );
        $modFooterImpressum = $this->DCATools->getModule(
            'Footer :: HTML :: Impressum',
            [
                'type' => 'html',
                'pid' => $theme->id
            ]
        );
        $modFooterDatenschutz = $this->DCATools->getModule(
            'Footer :: HTML :: Datenschutz',
            [
                'type' => 'html',
                'html' => '{{insert_article::footer-2}}',
                'pid' => $theme->id
            ]
        );

        $modFooterKontakt = $this->DCATools->getModule(
            'Footer :: Kontakt',
            [
                'type' => 'html',
                'pid' => $theme->id
            ]
        );

        $modHeaderArticle = $this->DCATools->getModule(
            'Header :: Artikel',
            [
                'type' => 'html',
                'html' => '{{insert_article::header-2}}',
                'pid' => $theme->id
            ]
        );

        $modFooterMenu = $this->DCATools->getModule(
            'Menu :: Footer',
            [
                'type' => 'customnav',
                'navigationTpl' => 'nav_default_bootstrap_footer',
                'pid' => $theme->id,
                'pages' => [
                    $modFooterHTML->id,
                    $modFooterDatenschutz->id,
                    $modFooterImpressum->id
                ]
            ]
        );


        $layout = $this->DCATools->getLayout(
            "Standard",
            [
                'template' => 'fe_page_bootstrap',
                'row' => '3rw',
                'modules' => [
                    [
                        'mod' => $modHeaderArticle->id,
                        'col' => 'header'
                    ],
                    [
                        'mod' => $modMainMenu->id,
                        'col' => 'header'
                    ],
                    [
                        'mod' => 0,
                        'col' => 'main'
                    ],
                    [
                        'mod' => $modFooterHTML->id,
                        'col' => 'footer'
                    ],
                    [
                        'mod' => $modFooterImpressum->id,
                        'col' => 'footer'
                    ],
                    [
                        'mod' => $modFooterDatenschutz->id,
                        'col' => 'footer'
                    ],
                    [
                        'mod' => $modFooterKontakt->id,
                        'col' => 'footer'
                    ]
                ],

                'pid' => $theme->id

            ]
        );


        $rootPage = $this->DCATools->getPage('Minkorrekt History', ['type' => 'root', 'fallback' => true]);


        if ($rootPage) {
            $hiddenPage = ['hide' => true];

            $sciencePage = $this->DCATools->getPage("Wissenschaft", [], $rootPage->id);

            $publisherPage = $this->DCATools->getPage("Verlag", [], $sciencePage->id);
            $publisherDetailPage = $this->DCATools->getPage("Verlagsinformationen", $hiddenPage, $publisherPage->id);

            $paperPage = $this->DCATools->getPage("Paper", [], $sciencePage->id);
            $paperDetailPage = $this->DCATools->getPage("Paper Details", $hiddenPage, $paperPage->id);

            $statisticsPage = $this->DCATools->getPage("Statistiken", [], $sciencePage->id);


            $this->DCATools->getPage("Folgen");

            $this->DCATools->getPage("Minkorrekt-Pool");
            $this->DCATools->getPage("Datenschutz");
            $this->DCATools->getPage("Impressum");
            $this->DCATools->getPage("Kontakt");


            $hiddenPageID = $this->DCATools->getPage("HIDDEN", $hiddenPage);

            $this->DCATools->getPage("Header", $hiddenPage, $hiddenPageID->id);
            $this->DCATools->getPage("Footer", $hiddenPage, $hiddenPageID->id);

            $newsPublisher = $this->DCATools->getNewsArchive("Publisher");
            $newsPublisher->jumpTo = $publisherDetailPage->id;
            $newsPublisher->save();


            $newsPaper = $this->DCATools->getNewsArchive("Paper");
            $newsPaper->jumpTo = $paperDetailPage->id;
            $newsPaper->save();
        }

        return Command::SUCCESS;
    }
}