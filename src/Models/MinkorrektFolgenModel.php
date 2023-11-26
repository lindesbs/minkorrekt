<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Models;

use Contao\Model;

class MinkorrektFolgenModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_minkorrekt_folgen';


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
