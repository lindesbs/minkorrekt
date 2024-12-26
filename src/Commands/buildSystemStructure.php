<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Commands;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\toolbox\Constants\Content;
use lindesbs\toolbox\Constants\Page;
use lindesbs\toolbox\Service\DCATools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'minkorrekt:buildSystem', description: 'Seiten, Module und sonstiges erstellen')]
class buildSystemStructure extends Command
{
    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        private readonly DCATools $DCATools,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->contaoFramework->initialize();

        Controller::loadDataContainer('tl_minkorrekt_paper');
        $io->writeln('Theme');

        $theme = $this->DCATools->getTheme('Standard');
        $newsPublisher = $this->DCATools->getNewsArchive('Publisher');
        $newsPaper = $this->DCATools->getNewsArchive('Paper');
        $newsMinkorrekt = $this->DCATools->getNewsArchive('Methodisch Inkorrekt');

        $io->writeln('Modules');
        $modMainMenu = $this->DCATools->getModule(
            'Menu :: Main Top',
            [
                'type' => 'navigation',
                'navigationTpl' => 'nav_default_bootstrap_header',
                'pid' => $theme->id,
            ]
        );

        $modFooterHTML = $this->DCATools->getModule(
            'Footer :: HTML',
            [
                'type' => 'html',
                'pid' => $theme->id,
            ]
        );
        $modFooterImpressum = $this->DCATools->getModule(
            'Footer :: HTML :: Impressum',
            [
                'type' => 'html',
                'pid' => $theme->id,
            ]
        );
        $modFooterDatenschutz = $this->DCATools->getModule(
            'Footer :: HTML :: Datenschutz',
            [
                'type' => 'html',
                'html' => '{{insert_article::footer-2}}',
                'pid' => $theme->id,
            ]
        );

        $modFooterKontakt = $this->DCATools->getModule(
            'Footer :: Kontakt',
            [
                'type' => 'html',
                'pid' => $theme->id,
            ]
        );

        $modHeaderArticle = $this->DCATools->getModule(
            'Header :: Artikel',
            [
                'type' => 'html',
                'html' => '{{insert_article::header-2}}',
                'pid' => $theme->id,
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
                    $modFooterImpressum->id,
                ],
            ]
        );

        $modFolgenLister = $this->DCATools->getModule(
            'News Folgen :: List',
            [
                'type' => 'newslist',
                'news_archives' => [$newsMinkorrekt->id],
                'pid' => $theme->id,
            ]
        );
        $modFolgenReader = $this->DCATools->getModule(
            'News Folgen :: Reader',
            [
                'type' => 'newsreader',
                'news_archives' => [$newsMinkorrekt->id],
                'pid' => $theme->id,
                'news_template' => 'news_paper_display',
            ]
        );


        $modPaperLister = $this->DCATools->getModule(
            'News Paper :: List',
            [
                'type' => 'newslist',
                'news_archives' => [$newsPaper->id],
                'pid' => $theme->id,
            ]
        );
        $modPaperReader = $this->DCATools->getModule(
            'News Paper :: Reader',
            [
                'type' => 'newsreader',
                'news_archives' => [$newsPaper->id],
                'pid' => $theme->id,
                'news_template' => 'news_paper_display',
            ]
        );

        $io->writeln('Layout');
        $layout = $this->DCATools->getLayout(
            'Standard',
            [
                'template' => 'fe_page_bootstrap',
                'row' => '3rw',
                'modules' => [
                    [
                        'mod' => $modHeaderArticle->id,
                        'col' => 'header',
                        'enable' => true,
                    ],
                    [
                        'mod' => $modMainMenu->id,
                        'col' => 'header',
                        'enable' => true,
                    ],
                    [
                        'mod' => 0,
                        'col' => 'main',
                        'enable' => true,
                    ],
                    [
                        'mod' => $modFooterHTML->id,
                        'col' => 'footer',
                        'enable' => true,
                    ],
                    [
                        'mod' => $modFooterImpressum->id,
                        'col' => 'footer',
                        'enable' => true,
                    ],
                    [
                        'mod' => $modFooterDatenschutz->id,
                        'col' => 'footer',
                        'enable' => true,
                    ],
                    [
                        'mod' => $modFooterKontakt->id,
                        'col' => 'footer',
                        'enable' => true,
                    ],
                ],

                'pid' => $theme->id,
            ]
        );

        $io->writeln('Pages');
        $rootPage = $this->DCATools->getPage(
            'Minkorrekt History',
            [
                Page::NOSSLROOT,
                'pageTitle' => 'Minkorrekt History - privates Projekt',
                'layout' => $layout->id,
            ],
            0
        );

        if ($rootPage) {
            $hiddenPage = ['hide' => true];

            $preaeambelPage = $this->DCATools->getPage('Praeambel', [], $rootPage->id);
            $sciencePage = $this->DCATools->getPage('Wissenschaft', [], $rootPage->id);

            $publisherPage = $this->DCATools->getPage('Verlag', [], $sciencePage->id);
            $publisherDetailPage = $this->DCATools->getPage('Verlagsinformationen', $hiddenPage, $publisherPage->id);

            $paperPage = $this->DCATools->getPage('Paper', [], $sciencePage->id);
            $paperArticle = $this->DCATools->getArticle('Listenansicht', [], $paperPage);
            $papaerOverviewModule = $this->DCATools->getContent(
                'Detail Liste der Paper',
                [
                    Content::TYPE => 'module',
                    Content::MODULE => $modPaperLister->id,
                ],
                $paperArticle,
                true
            );

            $paperDetailPage = $this->DCATools->getPage('Paper Details', $hiddenPage, $paperPage->id);
            $paperDetailArticle = $this->DCATools->getArticle('Detailansicht', [], $paperDetailPage);
            $papaerDetailModule = $this->DCATools->getContent(
                'Detailansicht der Paper',
                [
                    Content::TYPE => 'module',
                    Content::MODULE => $modPaperReader->id,
                ],
                $paperDetailArticle,
                true
            );

            $statisticsPage = $this->DCATools->getPage('Statistiken', [], $sciencePage->id);


            // -----------------------------------------------------------
            // Folgen
            $folgenPage = $this->DCATools->getPage('Folgen', [], $rootPage->id);
            $folgenDetailPage = $this->DCATools->getPage('Inhalt der Folge', [], $folgenPage->id);

            $folgenArticle = $this->DCATools->getArticle('Folgenansicht', [], $folgenPage);
            $folgenDetailArticle = $this->DCATools->getArticle('Detailansicht der Folge', [], $folgenDetailPage);

            // -----------------------------------------------------------

            $this->DCATools->getPage('Minkorrekt-Pool', [], $rootPage->id);
            $this->DCATools->getPage('Datenschutz', [], $rootPage->id);
            $this->DCATools->getPage('Impressum', [], $rootPage->id);
            $this->DCATools->getPage('Kontakt', [], $rootPage->id);

            $hiddenPageID = $this->DCATools->getPage('HIDDEN', $hiddenPage, $rootPage->id);

            $this->DCATools->getPage('Header', $hiddenPage, $hiddenPageID->id);
            $this->DCATools->getPage('Footer', $hiddenPage, $hiddenPageID->id);

            $newsPublisher = $this->DCATools->getNewsArchive('Publisher');
            $newsPublisher->jumpTo = $publisherDetailPage->id;
            $newsPublisher->save();

            $newsPaper = $this->DCATools->getNewsArchive('Paper');
            $newsPaper->jumpTo = $paperDetailPage->id;

            $newsPaper->save();

            $praeambelArticle = $this->DCATools->getArticle('Willkommen', [], $preaeambelPage);
            $praeambelArticle01 = $this->DCATools->getArticle('Inhalte', [], $preaeambelPage);
            $praeambelArticle02 = $this->DCATools->getArticle('Wozu der Wissenspool', [], $preaeambelPage);
            $praeambelArticle03 = $this->DCATools->getArticle('Hostorien', [], $preaeambelPage);
        }

        return Command::SUCCESS;
    }
}
