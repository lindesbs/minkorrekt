<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\src\Models;

use Contao\Model;

class MinkorrektPaperModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_paper';
}

class_alias(MinkorrektPaperModel::class, 'MinkorrektPaperModel');

