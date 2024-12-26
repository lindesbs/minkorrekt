<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use lindesbs\minkorrekt\Entity\PodcastEpisode;

class PodcastEpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PodcastEpisode::class);
    }
}