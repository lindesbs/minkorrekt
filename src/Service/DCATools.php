<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Service;

use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\StringUtil;

class DCATools
{

    public function getNewsArchive(string $title): NewsArchiveModel
    {
        $alias = StringUtil::generateAlias($title);
        $objNewsArchive = NewsArchiveModel::findOneBy('alias', $alias);

        if ($objNewsArchive) {
            return $objNewsArchive;
        }

        $objNewsArchive = new NewsArchiveModel();
        $objNewsArchive->tstamp = time();
        $objNewsArchive->perPage = 30;
        $objNewsArchive->title = $title;
        $objNewsArchive->alias = $alias;
        $objNewsArchive->save();

        return $objNewsArchive;
    }

    public function getPage(string $title, int $parent = 0): PageModel
    {
        $alias = StringUtil::generateAlias($title);
        $objPage = PageModel::findOneByAlias($alias);

        if ($objPage) {
            return $objPage;
        }


        $objPage = new PageModel();
        $objPage->tstamp = time();
        $objPage->title = $title;
        $objPage->alias = $alias;
        $objPage->pid = $parent;
        $objPage->type = 'regular';
        $objPage->language = 'de';
        $objPage->robots = 'index,follow';
        $objPage->enableCanonical = 1;
        $objPage->sitemap = 'map_default';
        $objPage->published = true;

        $objPage->save();

        return $objPage;
    }

}