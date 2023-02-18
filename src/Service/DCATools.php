<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\Service;

use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\ThemeModel;

class DCATools
{

    public function getModule(string $title, array $arrOptons, bool $bUpdate = false): ModuleModel
    {
        $alias = StringUtil::generateAlias($title);
        $objModule = ModuleModel::findOneBy('alias', $alias);

        if ($objModule) {
            if (!$bUpdate) {
                return $objModule;
            }
        }

        if ((!$bUpdate) || (!$objModule)) {
            $objModule = new ModuleModel();
        }

        $objModule->tstamp = time();
        $objModule->name = $title;
        $objModule->alias = $alias;

        foreach ($arrOptons as $key => $value) {
            if (is_array($value)) {
                $objModule->$key = serialize($value);
            } else {
                $objModule->$key = $value;
            }
        }


        $objModule->save();

        return $objModule;
    }


    public function getTheme(string $title, array $arrOptions = null, bool $bUpdate = false): ThemeModel
    {
        $alias = StringUtil::generateAlias($title);
        $objTheme = ThemeModel::findOneBy('alias', $alias);

        if ($objTheme) {
            return $objTheme;
        }

        $objTheme = new ThemeModel();
        $objTheme->tstamp = time();
        $objTheme->name = $title;
        $objTheme->alias = $alias;

        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $value) {
                if (is_array($value)) {
                    $objTheme->$key = serialize($value);
                } else {
                    $objTheme->$key = $value;
                }
            }
        }

        $objTheme->save();

        return $objTheme;
    }

    public function getLayout(string $title, array $arrOptions, int $parent = 0): LayoutModel
    {
        $alias = StringUtil::generateAlias($title);
        $objLayout = LayoutModel::findOneBy('alias', $alias);

        if ($objLayout) {
            return $objLayout;
        }

        $objLayout = new LayoutModel();
        $objLayout->tstamp = time();

        if ($parent !== 0) {
            $objLayout->pid = $parent;
        }

        $objLayout->name = $title;
        $objLayout->alias = $alias;
        $objLayout->row = "3rw";
        $objLayout->headerHeight = serialize([
            'unit' => '',
            'value' => ''
        ]);
        $objLayout->footerHeight = serialize([
            'unit' => '',
            'value' => ''
        ]);

        $objLayout->template = $this->getConfig('template', 'fe_page', $arrOptons);
        $objLayout->viewport = $this->getConfig('viewport', 'width=device-width, initial-scale=1.0', $arrOptons);


        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $value) {
                if (is_array($value)) {
                    $objLayout->$key = serialize($value);
                } else {
                    $objLayout->$key = $value;
                }
            }
        }

        $objLayout->save();

        return $objLayout;
    }


    public function getNewsArchive(string $title, array $arrOptions = []): NewsArchiveModel
    {
        $alias = StringUtil::generateAlias($title);
        $objNewsArchive = NewsArchiveModel::findOneBy('alias', $alias);

        if ($objNewsArchive) {
            return $objNewsArchive;
        }

        $objNewsArchive = new NewsArchiveModel();
        $objNewsArchive->tstamp = time();
        $objNewsArchive->perPage = $this->getConfig('perPage', 30, $arrOptions);
        $objNewsArchive->title = $title;
        $objNewsArchive->alias = $alias;

        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $value) {
                if (is_array($value)) {
                    $objNewsArchive->$key = serialize($value);
                } else {
                    $objNewsArchive->$key = $value;
                }
            }
        }


        $objNewsArchive->save();

        return $objNewsArchive;
    }

    public function getPage(string $title, array $arrOptions = [], int $parent = 0): PageModel
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

        if (is_array($arrOptions)) {
            foreach ($arrOptions as $key => $value) {
                if (is_array($value)) {
                    $objPage->$key = serialize($value);
                } else {
                    $objPage->$key = $value;
                }
            }
        }


        $objPage->save();

        return $objPage;
    }


    private function getConfig(string $key, mixed $default, mixed $heap = null)
    {
        if ($heap === null) {
            return $default;
        }

        if (is_numeric($heap)) {
            return $heap;
        }

        if (array_key_exists($key, $heap)) {
            return $heap[$key];
        }
        return $default;
    }
}