<?php
declare(strict_types=1);

namespace lindesbs\minkorrekt\Service;

use Contao\StringUtil;
use lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Cache\ItemInterface;

class WebsiteScraper
{

    public function scrape(MinkorrektPaperModel $paper)
    {

        $filesystemAdapter = new FilesystemAdapter();

        $html = $filesystemAdapter->get(
            'WEPAGE_'.StringUtil::generateAlias($paper->url),
            static function (ItemInterface $item) use ($paper): string|bool {
                $item->expiresAfter(86400);

                return file_get_contents($paper->url);
                ;
            }
        );

        $crawler = new Crawler($html);
        $metaTags = $crawler->filter('head meta')->each(fn($node) => [
            'name' => $node->attr('name'),
            'content' => $node->attr('content')
        ]);

        $arrIgnoreNames = ['applicable-device','viewport','msapplication-TileColor','msapplication-config',
            'theme-color','application-name', 'robots', 'access',
            'dc.source',
            'dc.format',
            'dc.publisher',
            'dc.date',
            'dc.identifier',
            'DOI',
            'access_endpoint',
            'twitter:card',
            'twitter:image:alt',
            'twitter:title',
            'twitter:description'
        ];

        $arrNotYetImplemented = [
            'citation_journal_title',
            'citation_journal_abbrev',
            'citation_publisher',
            'citation_language',
            'citation_reference',
            'citation_author_institution',
            'twitter:card',
            'twitter:image:alt',
            'twitter:title',
            'twitter:description',
            'twitter:image',
            'citation_volume',
            'citation_issue'
        ];

        foreach ($metaTags as $meta) {
            if (!$meta['name']) {
                continue;
            }

            if (in_array($meta['name'], $arrIgnoreNames)) {
                continue;
            }
            if (in_array($meta['name'], $arrNotYetImplemented)) {
                continue;
            }

            if ($meta['name'] === 'dc.creator') {
                $objCreator = MinkorrektPaperCreatorModel::findBy('name', $meta['name']);
                if (!$objCreator ) {
                    $objCreator = new MinkorrektPaperCreatorModel();
                }

                $objCreator->pid=$paper->id;
                $objCreator->name = $meta['name'];
                $objCreator->alias = StringUtil::generateAlias($meta['name']);
                $objCreator->save();
                continue;
            }

            if ($meta['name'] === 'journal_id') {
                $objJournal = MinkorrektPublisherModel::findOneBy('journal_id', $meta['content']);

                if (!$objJournal) {
                    $objJournal = new MinkorrektPublisherModel();
                    $objJournal->journal_id = $meta['content'];
                    $objJournal->title = sprintf("--NOT YET SET -- %s", $paper->alias);
                    $objJournal->save();
                }

                $paper->pid=$objJournal->id;
                $paper->save();

                continue;
            }

            if ($meta['name'] === 'dc.title') {
                $paper->title = $meta['content'];
                $paper->save();

                continue;
            }
            if ($meta['name'] === 'citation_title') {
                $paper->citation_title = $meta['content'];
                $paper->save();

                continue;
            }



            if ($meta['name'] === 'citation_online_date') {
                // Erstmal ignorieren

                $date = \DateTime::createFromFormat("Y/d/m", $meta['content']);
                $paper->citation_online_date = $date->getTimestamp();
                $paper->save();

                continue;
            }

            if ($meta['name'] === 'dc.language') {
                $paper->language = strtolower((string) $meta['content']);
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'dc.copyright') {
                $paper->copyright = strtolower((string) $meta['content']);
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'dc.rights') {
                $paper->rights = strtolower((string) $meta['content']);
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'dc.rightsAgent') {
                $paper->rightsAgent = $meta['content'];
                $paper->save();
                continue;
            }
            if (($meta['name'] === 'dc.description') && (isset($meta['content']))) {
                $paper->description = $meta['content'];
                $paper->save();
                continue;
            }
            if (($meta['name'] === 'description') && (isset($meta['content']))) {
                $paper->description = $meta['content'];
                $paper->save();
                continue;
            }



            if (str_starts_with((string) $meta['name'], 'prism')) {
                // Erstmal ignorieren
                continue;
            }

            if ($meta['name'] === 'citation_pdf_url') {
                $paper->citation_pdf_url = $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_fulltext_html_url') {
                $paper->citation_fulltext_html_url = $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_issn') {
                $paper->citation_issn = $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_lastpage') {
                $paper->citation_lastpage = (int) $meta['content'];
                $paper->save();
                continue;
            }
            if ($meta['name'] === 'citation_firstpage') {
                $paper->citation_firstpage = (int) $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_article_type') {
                $paper->citation_article_type = $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'size') {
                $paper->size = (int) $meta['content'];
                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_springer_api_url') {
                $paper->citation_springer_api_url = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'citation_doi') {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'dc.subject') {
                if (!isset($paper->subjects)) {
                    $paper->subjects='';
                }

                $arrSubjects = explode(",", (string) $paper->subjects);
                $arrSubjects[]= $meta['content'];

                $arrSubjects = array_unique($arrSubjects, SORT_REGULAR);
                sort($arrSubjects);
                $paper->subjects = implode(",", $arrSubjects);

                $paper->save();
                continue;
            }

            if ($meta['name'] === 'citation_author') {
                $alias = StringUtil::generateAlias($meta['content']);
                $author = MinkorrektPaperCreatorModel::findOneBy('alias', $alias);

                if (!$author) {
                    $author = new MinkorrektPaperCreatorModel();
                    $author->alias = $alias;
                    $author->pid = $paper->id;
                }

                $author->name = $meta['content'];
                $author->save();
                continue;
            }


            if ($meta['name'] === 'dc.type') {
                $paper->paperType = $meta['content'];

                $paper->save();
                continue;
            }


            if ($meta['name'] === 'twitter:site') {
                $paper->twitter = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'citation_doi') {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'citation_doi') {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'citation_doi') {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }


            if ($meta['name'] === 'citation_doi') {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }



            dd($meta);
        }
    }
}