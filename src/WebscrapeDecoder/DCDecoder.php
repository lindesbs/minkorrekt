<?php

declare(strict_types=1);

namespace lindesbs\minkorrekt\WebscrapeDecoder;

use Contao\StringUtil;
use lindesbs\minkorrekt\Interface\WebscraperPaperDecoderInterface;
use lindesbs\minkorrekt\Models\MinkorrektPaperCreatorModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperModel;
use lindesbs\minkorrekt\Models\MinkorrektPaperTagsModel;

class DCDecoder implements WebscraperPaperDecoderInterface
{

    public function decode(string $strKey, string $strContent, MinkorrektPaperModel $paperModel): bool
    {
        $arrNotYetImplemented = [
            'dc.date',
            'dc.format',
            'dc.identifier',
            'dc.publisher',
            'dc.rightsagent',
            'dc.source'
        ];

        $strKey = strtolower($strKey);

        if (\in_array($strKey, $arrNotYetImplemented, true)) {
            return true;
        }

        if ('dc.creator' === $strKey) {
            $objCreator = MinkorrektPaperCreatorModel::findBy('name', $strKey);

            if (!$objCreator) {
                $objCreator = new MinkorrektPaperCreatorModel();
            }

            $objCreator->pid = $paperModel->id;
            $objCreator->name = $strKey;
            $objCreator->alias = StringUtil::generateAlias($strKey);

            return true;
        }

        if ('dc.title' === $strKey) {
            $paperModel->title = $strContent;

            return true;
        }


        if ('dc.language' === strtolower($strKey)) {
            $paperModel->language = strtolower((string)$strContent);

            return true;
        }

        if ('dc.copyright' === $strKey) {
            $paperModel->copyright = strtolower((string)$strContent);

            return true;
        }

        if ('dc.rights' === $strKey) {
            $paperModel->rights = strtolower((string)$strContent);

            return true;
        }

        if ('dc.rightsAgent' === $strKey) {
            $paperModel->rightsAgent = $strContent;

            return true;
        }

        if (('dc.description' === strtolower($strKey)) && (isset($strContent))) {
            $paperModel->description = $strContent;

            return true;
        }

        if ('dc.subject' === $strKey) {
            if (!isset($paperModel->subjects)) {
                $paperModel->subjects = '';
            }

            $arrSubjects = explode(',', (string)$paperModel->subjects);
            $arrSubjects[] = $strContent;

            foreach ($arrSubjects as $sub) {
                $alias = StringUtil::generateAlias($sub);
                $tags = MinkorrektPaperTagsModel::findOneBy('alias', $alias);
                if (!$tags) {
                    $tags = new MinkorrektPaperTagsModel();
                }

                $tags->name = $sub;
                $tags->alias = $alias;

                $tags->save();
            }
            return true;
        }


        if ('dc.type' === strtolower($strKey)) {
            $paperModel->paperType = $strContent;

            return true;
        }


        return false;
    }
}