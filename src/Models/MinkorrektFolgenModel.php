<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Date;
use Contao\Model;

class MinkorrektFolgenModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_folgen';


    public static function findByPublished(array $arrOptions=[])
    {
        $t = static::$strTable;
        $arrColumns = array("$t.published=?");

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.pubdate";
        }

        return static::findBy($arrColumns, [true], $arrOptions);
    }

    public function getWip(): ?string
    {
        return $this->__get('wip');
    }

    public function setWip(string $wip): void
    {
        $this->__set('wip', $wip);
    }



}

class_alias(MinkorrektFolgenModel::class, 'MinkorrektFolgenModel');
