<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Model;

class MinkorrektPublisherModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_publisher';
}

class_alias(MinkorrektPublisherModel::class, 'MinkorrektPublisherModel');
