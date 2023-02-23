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
    public function scrape(MinkorrektPaperModel $paper): void
    {
        $filesystemAdapter = new FilesystemAdapter();

        $html = $filesystemAdapter->get(
            'WEPAGE_' . StringUtil::generateAlias($paper->url),
            static function (ItemInterface $item) use ($paper): string|bool {
                $item->expiresAfter(86400);

                return file_get_contents($paper->url);
            }
        );

        $crawler = new Crawler($html);
        $metaTags = $crawler->filter('head meta')->each(static fn($node) => [
            'name' => $node->attr('name'),
            'content' => $node->attr('content'),
        ]);

        $arrIgnoreNames = [
            'applicable-device',
            'viewport',
            'msapplication-TileColor',
            'msapplication-config',
            'theme-color',
            'application-name',
            'robots',
            'access',
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
            'twitter:description',
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
            'citation_issue',
            'citation_fulltext_world_readable',
        ];

        foreach ($metaTags as $meta) {
            if (!$meta['name']) {
                continue;
            }

            if (\in_array($meta['name'], $arrIgnoreNames, true)) {
                continue;
            }

            if (\in_array($meta['name'], $arrNotYetImplemented, true)) {
                continue;
            }

            if ('dc.creator' === $meta['name']) {
                $objCreator = MinkorrektPaperCreatorModel::findBy('name', $meta['name']);

                if (!$objCreator) {
                    $objCreator = new MinkorrektPaperCreatorModel();
                }

                $objCreator->pid = $paper->id;
                $objCreator->name = $meta['name'];
                $objCreator->alias = StringUtil::generateAlias($meta['name']);
                $objCreator->save();
                continue;
            }

            if ('journal_id' === $meta['name']) {
                $objJournal = MinkorrektPublisherModel::findOneBy('journal_id', $meta['content']);

                if (!$objJournal) {
                    $objJournal = new MinkorrektPublisherModel();
                    $objJournal->journal_id = $meta['content'];
                    $objJournal->title = sprintf('--NOT YET SET -- %s', $paper->alias);
                    $objJournal->save();
                }

                $paper->pid = $objJournal->id;
                $paper->save();

                continue;
            }

            if ('dc.title' === $meta['name']) {
                $paper->title = $meta['content'];
                $paper->save();

                continue;
            }

            if ('citation_title' === $meta['name']) {
                $paper->citation_title = $meta['content'];
                $paper->save();

                continue;
            }

            if ('citation_online_date' === $meta['name']) {
                // Erstmal ignorieren

                $date = \DateTime::createFromFormat('Y/d/m', $meta['content']);
                $paper->citation_online_date = $date->getTimestamp();
                $paper->save();

                continue;
            }

            if ('dc.language' === $meta['name']) {
                $paper->language = strtolower((string)$meta['content']);
                $paper->save();
                continue;
            }

            if ('dc.copyright' === $meta['name']) {
                $paper->copyright = strtolower((string)$meta['content']);
                $paper->save();
                continue;
            }

            if ('dc.rights' === $meta['name']) {
                $paper->rights = strtolower((string)$meta['content']);
                $paper->save();
                continue;
            }

            if ('dc.rightsAgent' === $meta['name']) {
                $paper->rightsAgent = $meta['content'];
                $paper->save();
                continue;
            }

            if (('dc.description' === $meta['name']) && (isset($meta['content']))) {
                $paper->description = $meta['content'];
                $paper->save();
                continue;
            }

            if (('description' === $meta['name']) && (isset($meta['content']))) {
                $paper->description = $meta['content'];
                $paper->save();
                continue;
            }

            if (str_starts_with((string)$meta['name'], 'prism')) {
                // Erstmal ignorieren
                continue;
            }

            if ('citation_pdf_url' === $meta['name']) {
                $paper->citation_pdf_url = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_fulltext_html_url' === $meta['name']) {
                $paper->citation_fulltext_html_url = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_issn' === $meta['name']) {
                $paper->citation_issn = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_lastpage' === $meta['name']) {
                $paper->citation_lastpage = (int)$meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_firstpage' === $meta['name']) {
                $paper->citation_firstpage = (int)$meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_article_type' === $meta['name']) {
                $paper->citation_article_type = $meta['content'];
                $paper->save();
                continue;
            }

            if ('size' === $meta['name']) {
                $paper->size = (int)$meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_springer_api_url' === $meta['name']) {
                $paper->citation_springer_api_url = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_doi' === $meta['name']) {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }

            if ('dc.subject' === $meta['name']) {
                if (!isset($paper->subjects)) {
                    $paper->subjects = '';
                }

                $arrSubjects = explode(',', (string)$paper->subjects);
                $arrSubjects[] = $meta['content'];

                $arrSubjects = array_unique($arrSubjects, SORT_REGULAR);
                sort($arrSubjects);
                $paper->subjects = implode(',', $arrSubjects);

                $paper->save();
                continue;
            }

            if ('citation_author' === $meta['name']) {
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

            if ('dc.type' === $meta['name']) {
                $paper->paperType = $meta['content'];

                $paper->save();
                continue;
            }

            if ('twitter:site' === $meta['name']) {
                $paper->twitter = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_publication_date' === $meta['name']) {
                // Datums kann manchmal nur Y/m sein
                $arrDate = explode('/', (string)$meta['content']);
                $srcDate = sprintf(
                    '%s/%s/%s',
                    $arrDate[0],
                    3 === \count($arrDate) ?
                        $arrDate[2] : '1',
                    $arrDate[1],
                );

                $date = \DateTime::createFromFormat('Y/d/m', $srcDate);

                $paper->publishedAt = $date->getTimestamp();
                $paper->save();
                continue;
            }

            if ('citation_doi' === $meta['name']) {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_doi' === $meta['name']) {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }

            if ('citation_doi' === $meta['name']) {
                $paper->doi = $meta['content'];
                $paper->save();
                continue;
            }

            dd($meta);
        }
    }
}
