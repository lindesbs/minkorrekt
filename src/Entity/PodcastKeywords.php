<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use lindesbs\minkorrekt\Repository\PodcastKeywordsRepository;

#[Entity(repositoryClass: PodcastKeywordsRepository::class)]
#[Table(name: 'mh_podcast_keywords')]
class PodcastKeywords implements \Stringable
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;


    #[Column(type: 'string', length: 255)]
    private string $name;


    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $alias = null;

    #[Column(type: 'integer', options: ['default' => 0])]
    private int $usageCount = 0;

    public function __construct()
    {
    }


    #[\Override]
    public function __toString(): string
    {
        return $this->name ?? 'Undefined Keyword';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}