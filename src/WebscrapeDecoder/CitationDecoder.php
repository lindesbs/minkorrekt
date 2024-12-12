<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use Contao\StringUtil;
use lindesbs\minkorrekt\Interface\WebscraperPaperDecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;

class CitationDecoder implements WebscraperPaperDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool
    {
        $arrNotYetImplemented = [
            "citation_author_institution",
            "citation_issue",
            "citation_journal_abbrev",
            "citation_journal_title",
            "citation_language",
            "citation_publisher",
            "citation_reference",
            "citation_volume",
            'citation_abstract_html_url',
            'citation_author_orcid',
            'citation_xml_url',
            'citation_author_email',
            'citation_abstract',
            'citation_date',
            'citation_keywords',
            'citation_pmid'
        ];

        $strKey = strtolower($strKey);

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }

        if ('citation_title' === $strKey) {
            $paperModel->citation_title = $strContent;

            return true;
        }

        if ('citation_online_date' === $strKey) {
            // Erstmal ignorieren

            $date = \DateTime::createFromFormat('Y/d/m', $strContent);

            if (!$date) {
                $date = \DateTime::createFromFormat('Y-m-d', $strContent);
            }

            if (!$date) {
                dd($strContent);
            }

            $paperModel->citation_online_date = $date->getTimestamp();

            return true;
        }


        if ('citation_pdf_url' === $strKey) {
            $paperModel->citation_pdf_url = $strContent;

            return true;
        }

        if ('citation_fulltext_html_url' === $strKey) {
            $paperModel->citation_fulltext_html_url = $strContent;

            return true;
        }

        if ('citation_issn' === $strKey) {
            $paperModel->citation_issn = $strContent;

            return true;
        }

        if ('citation_lastpage' === $strKey) {
            $paperModel->citation_lastpage = min((int)$strContent, 65000);

            return true;
        }

        if ('citation_firstpage' === $strKey) {
            $paperModel->citation_firstpage = (int)$strContent;

            return true;
        }

        if ('citation_article_type' === $strKey) {
            $paperModel->citation_article_type = $strContent;

            return true;
        }


        if ('citation_springer_api_url' === $strKey) {
            $paperModel->citation_springer_api_url = $strContent;

            return true;
        }

        if ('citation_doi' === $strKey) {
            $paperModel->doi = $strContent;

            return true;
        }

        if ('citation_author' === $strKey) {
            $alias = StringUtil::generateAlias($strContent);
            $author = MinkorrektPaperCreatorModel::findOneBy('alias', $alias);

            if (!$author) {
                $author = new MinkorrektPaperCreatorModel();
                $author->alias = $alias;
                $author->pid = $paperModel->id;
            }

            $author->name = $strContent;

            return true;
        }


        if ('citation_publication_date' === $strKey) {
            // Datum kann manchmal nur Y/m sein

            $srcDate = strtotime($strContent);


            if (strlen($strContent) == 7) {
                // 2023/01
                $srcDate = \DateTime::createFromFormat("Y/m", $strContent)->getTimestamp();
            }


            if (!$srcDate) {
                dump($srcDate);
                dd($strContent);
            }
            $paperModel->publishedAt = $srcDate;

            return true;
        }

        if ('citation_doi' === $strKey) {
            $paperModel->doi = $strContent;

            return true;
        }

        if ('citation_doi' === $strKey) {
            $paperModel->doi = $strContent;

            return true;
        }

        if ('citation_doi' === $strKey) {
            $paperModel->doi = $strContent;

            return true;
        }

        return false;
    }
}