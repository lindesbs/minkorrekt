<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use lindesbs\minkorrekt\Repository\PodcastThemaRepository;


#[Entity(repositoryClass: PodcastThemaRepository::class)]
#[Table(name: 'mh_podcast_passage')]
class PodcastPassage
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer')]
    private int $order;

    #[Column(type: 'text')]
    private ?string $text = null;

    #[Column(type: 'string', unique: true)]
    private int $thema_alias;

    #[Column(type: 'string', length: 100)]
//
//ThemenArt::THEMA,
//ThemenArt::UEBERSCHRIFT,
//ThemenArt::SCHWURBEL,
//ThemenArt::GADGET,
//ThemenArt::TIMETABLE,
//ThemenArt::SCHWURBEL,
//ThemenArt::TEXT,
//ThemenArt::EXPERIMENT,
//ThemenArt::SNACKABLESCIENCE,
//ThemenArt::BEGRUESSUNG,
//ThemenArt::KOMMENTAR,
//ThemenArt::HAUSMEISTEREI
    private string $type;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $paper = null;
}