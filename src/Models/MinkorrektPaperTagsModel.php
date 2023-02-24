<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Model;

class MinkorrektPaperTagsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_paper_tags';
}

class_alias(MinkorrektPaperTagsModel::class, 'MinkorrektPaperTagsModel');
