<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\src\Models;

use Contao\Model;

class MinkorrektPublisherModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_publisher';
}

class_alias(MinkorrektPublisherModel::class, 'MinkorrektPublisherModel');

