<?php

namespace lindesbs\minkorrekt\Service;

use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;

class GenerateStatistics
{
    private int $countEpisoden;
    private int $ersteFolge = 1;
    private int $letzteFolge = 2;

    private int $gesamtLaenge;

    public function __construct(
        private readonly ContaoFramework $contaoFramework
    ) {
    }

    public function render()
    {
        $this->contaoFramework->initialize();

        $objFolgen = MinkorrektFolgenModel::findAll();

        $this->setCountEpisoden($objFolgen === null ? 0 : count($objFolgen));

        $ersteFolge = time();
        $letzteFolge = 0;
        $gesamtLaenge = 0;

        foreach ($objFolgen as $folge) {
            $ersteFolge = min($ersteFolge, $folge->pubdate);
            $letzteFolge = max($letzteFolge, $folge->pubdate);
            $gesamtLaenge += $folge->duration;
        }

        $this->setErsteFolge($ersteFolge);
        $this->setLetzteFolge($letzteFolge);

        $this->setGesamtLaenge($gesamtLaenge);
    }

    public function getCountEpisoden(): int
    {
        return $this->countEpisoden;
    }

    public function setCountEpisoden(int $countEpisoden): void
    {
        $this->countEpisoden = $countEpisoden;
    }

    public function getErsteFolge(): int
    {
        return $this->ersteFolge;
    }

    public function setErsteFolge(int $ersteFolge): void
    {
        $this->ersteFolge = $ersteFolge;
    }

    public function getLetzteFolge(): int
    {
        return $this->letzteFolge;
    }

    public function setLetzteFolge(int $letzteFolge): void
    {
        $this->letzteFolge = $letzteFolge;
    }

    public function getGesamtLaenge(): int
    {
        return $this->gesamtLaenge;
    }

    public function setGesamtLaenge(int $gesamtLaenge): void
    {
        $this->gesamtLaenge = $gesamtLaenge;
    }


}