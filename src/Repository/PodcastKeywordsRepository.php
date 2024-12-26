<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use lindesbs\minkorrekt\Entity\PodcastEpisode;
use lindesbs\minkorrekt\Entity\PodcastKeywords;

class PodcastKeywordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PodcastKeywords::class);
    }
}