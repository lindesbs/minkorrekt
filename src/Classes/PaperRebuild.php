<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Classes;

use Contao\Backend;
use Contao\Controller;

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
            $alias = sprintf('F%sT%s', $data['minkorrekt_thema_folge'], $data['minkorrekt_thema_nummer']);

            $sqlPaper = sprintf("SELECT id FROM tl_minkorrekt_paper WHERE alias='%s' LIMIT 0,1", $alias);
            $objPaper = $this->Database->execute($sqlPaper);

            if (0 === $objPaper->numRows) {
                $arrData = [
                    'tstamp' => time(),
                    'alias' => $alias,
                    'published' => false,
                    'tlContentId' => $data['id'],
                    'tlNewsId' => $data['pid'],
                ];

                $sqlInsert = 'INSERT INTO tl_minkorrekt_paper %s';
                $this->Database->prepare($sqlInsert)->set($arrData)->execute();
            }
        }
        Controller::redirect('contao?do=paper');
    }
}
