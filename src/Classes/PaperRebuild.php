<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Classes;

use Contao\Backend;
use Contao\Controller;
use Contao\StringUtil;
use Contao\System;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPublisherModel;

class PaperRebuild extends Backend
{
    public function rebuild(): void
    {
//        if ($_SERVER['APP_ENV'] === 'dev') {
//            $this->Database->execute("TRUNCATE TABLE tl_minkorrekt_paper");
//        }

        $sql = "SELECT * FROM tl_content WHERE ptable='tl_news' AND minkorrekt_thema_art='THEMA'";
        $result = $this->Database->execute($sql);

        while ($result->next()) {
            $data = $result->row();
            $aliasPaper = sprintf('F%sT%s', $data['minkorrekt_thema_folge'], $data['minkorrekt_thema_nummer']);

            $pattern = '/(https?|ftp):\/\/[^\s\/$.?#].[^\s]*/i';
            $url = 'unknown';

            if (preg_match($pattern, (string) $data['text'], $matches)) {
                $url = $matches[0];

                $decodedUrl = parse_url($url);
                $alias = StringUtil::generateAlias($decodedUrl['host']);
                $publisher = MinkorrektPublisherModel::findByIdOrAlias($alias);

                if (!$publisher) {
                    $publisher = new MinkorrektPublisherModel();

                    $publisher->tstamp = time();
                    $publisher->alias = $alias;

                    $publisher->url = sprintf("%s://%s/", $decodedUrl['scheme'], $decodedUrl['host']);
                    $publisher->title = $decodedUrl['host'];

                    $publisher->save();
                }
            }

            $objPaper = MinkorrektPaperModel::findByIdOrAlias($aliasPaper);

            if (!$objPaper) {
                $objPaper = new MinkorrektPaperModel();

                $objPaper->tstamp = time();
                $objPaper->alias = $aliasPaper;
            }
            $objPaper->published = false;
            $objPaper->tlContentId = $data['id'];
            $objPaper->tlNewsId = $data['pid'];
            $objPaper->url = trim($url, "'\"");

            if ($objPaper->url) {
                $objPaper->title = $aliasPaper;
                System::getContainer()->get('lindesbs.minkorrekt.websitescrape')->scrape($objPaper);
            }


            $objPaper->save();
        }


        Controller::redirect('contao?do=paper');
    }
}
