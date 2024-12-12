<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Model;

class MinkorrektThemenModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_themen';
}

class_alias(MinkorrektThemenModel::class, 'MinkorrektThemenModel');
