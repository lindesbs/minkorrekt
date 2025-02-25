<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
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
    private int $sorting;

    #[Column(type: 'text')]
    private ?string $content = null;

    #[Column(type: 'string', unique: true)]
    private string $thema_alias;

    #[Column(type: 'integer')]
    private int $themaNr;

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
    private string $themaArt = 'THEMA';

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $paper = null;



    #[ORM\ManyToOne(targetEntity: PodcastEpisode::class, inversedBy: 'passages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PodcastEpisode $episode = null;


    public function getId(): int
    {
        return $this->id;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }



    public function getPaper(): ?string
    {
        return $this->paper;
    }

    public function setPaper(?string $paper): void
    {
        $this->paper = $paper;
    }

    public function getThemaArt(): string
    {
        return $this->themaArt;
    }

    public function setThemaArt(string $themaArt): void
    {
        $this->themaArt = $themaArt;
    }

    public function getThemaNr(): int
    {
        return $this->themaNr;
    }

    public function setThemaNr(int $themaNr): void
    {
        $this->themaNr = $themaNr;
    }

    public function getThemaAlias(): string
    {
        return $this->thema_alias;
    }

    public function setThemaAlias(string $thema_alias): void
    {
        $this->thema_alias = $thema_alias;
    }


    public function getEpisode(): ?PodcastEpisode
    {
        return $this->episode;
    }
    public function setEpisode(?PodcastEpisode $episode): self
    {
        $this->episode = $episode;
        return $this;
    }



}