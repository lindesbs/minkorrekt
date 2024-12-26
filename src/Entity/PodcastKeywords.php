<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Ausi\SlugGenerator\SlugGenerator;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use lindesbs\minkorrekt\Repository\PodcastKeywordsRepository;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: PodcastKeywordsRepository::class)]
#[\Doctrine\ORM\Mapping\Table(name: 'podcast_keywords')]
class PodcastKeywords
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;


    #[Column(type: 'string', length: 255)]
    private string $name;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $alias = null;

    private $slug;

    /**
     * @param string $name
     */
    public function __construct()
    {
        $this->slug = new SlugGenerator();
    }

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

        $this->alias = $this->slug->generate($name);

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