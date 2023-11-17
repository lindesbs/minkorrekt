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

        $this->setCountEpisoden(count($objFolgen));

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

    /**
     * @return int
     */
    public function getCountEpisoden(): int
    {
        return $this->countEpisoden;
    }

    /**
     * @param int $countEpisoden
     */
    public function setCountEpisoden(int $countEpisoden): void
    {
        $this->countEpisoden = $countEpisoden;
    }

    /**
     * @return int
     */
    public function getErsteFolge(): int
    {
        return $this->ersteFolge;
    }

    /**
     * @param int $ersteFolge
     */
    public function setErsteFolge(int $ersteFolge): void
    {
        $this->ersteFolge = $ersteFolge;
    }

    /**
     * @return int
     */
    public function getLetzteFolge(): int
    {
        return $this->letzteFolge;
    }

    /**
     * @param int $letzteFolge
     */
    public function setLetzteFolge(int $letzteFolge): void
    {
        $this->letzteFolge = $letzteFolge;
    }

    /**
     * @return int
     */
    public function getGesamtLaenge(): int
    {
        return $this->gesamtLaenge;
    }

    /**
     * @param int $gesamtLaenge
     */
    public function setGesamtLaenge(int $gesamtLaenge): void
    {
        $this->gesamtLaenge = $gesamtLaenge;
    }


}