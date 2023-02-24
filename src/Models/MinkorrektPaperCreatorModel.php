<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Model;

class MinkorrektPaperCreatorModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_paper_creator';
}

class_alias(MinkorrektPaperCreatorModel::class, 'MinkorrektPaperCreatorModel');
