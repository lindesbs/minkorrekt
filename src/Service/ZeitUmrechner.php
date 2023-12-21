<?php

namespace lindesbs\minkorrekt\Service;

use DateTimeImmutable;

class ZeitUmrechner
{
    public function convert(int $seconds): \DateInterval
    {
        $dtNow = new DateTimeImmutable('@0');
        $dtLaenge = new DateTimeImmutable('@' . $seconds);
        return $dtLaenge->diff($dtNow);
    }
}