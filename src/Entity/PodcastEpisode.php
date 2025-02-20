<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;


use Ausi\SlugGenerator\SlugGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use lindesbs\minkorrekt\Constants\BearbeitungsStatus;
use lindesbs\minkorrekt\Constants\FolgenTyp;
use lindesbs\minkorrekt\Repository\PodcastEpisodeRepository;

#[Entity(repositoryClass: PodcastEpisodeRepository::class)]
#[Table(name: 'mh_podcast_episode')]
class PodcastEpisode
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 255)]
    private string $title;

    #[Column(type: 'text')]
    private string $description;

    #[Column(type: 'string', length: 255, nullable: true)]
    private string $subtitle;

    #[Column(type: 'integer', nullable: true)]
    private int $episode;

    #[Column(type: 'string', length: 255, nullable: true)]
    private string $link;

    #[Column(type: 'string', length: 255, unique: true)]
    private string $guid;

    #[Column(type: 'boolean', nullable: true)]
    private bool $explicit;

    #[Column(type: 'string', length: 255, nullable: true)]
    private string $author;

    #[Column(type: 'string', length: 255, nullable: true)]
    private string $slug;

    #[Column(type: 'integer', options: ["default" => 0])]
    private int $duration = 0;

    #[Column(type: 'boolean', options: ["default" => false])]
    private bool $enclosure = false;

    #[Column(type: 'datetime')]
    private \DateTime $pubdate;

    #[Column(type: 'string', length: 16, options: ["default" => BearbeitungsStatus::UNBEARBEITET])]
    private string $status = BearbeitungsStatus::UNBEARBEITET;

    #[Column(type: 'string', length: 16, options: ["default" => FolgenTyp::STANDARD])]
    private string $folgentyp = FolgenTyp::STANDARD;

    /**
     * Many Users have Many Groups.
     * @var Collection<int, PodcastKeywords>
     */
    #[JoinTable(name: 'mh_join_podcast_keywords')]
    #[JoinColumn(name: 'episode_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'keyword_id', referencedColumnName: 'id')]
    #[ManyToMany(targetEntity: PodcastKeywords::class)]
    private Collection $keywords;


    #[JoinTable(name: 'mh_join_podcast_thema')]
    #[JoinColumn(name: 'episode_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'thema_id', referencedColumnName: 'id')]
    #[ManyToMany(targetEntity: PodcastPassage::class)]
    private Collection $thema;




    public function __construct()
    {
        $this->keywords = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title ?? 'Undefined Keyword';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getEpisode(): int
    {
        return $this->episode;
    }

    public function setEpisode(int $episode): void
    {
        $this->episode = $episode;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    public function setExplicit(bool $explicit): void
    {
        $this->explicit = $explicit;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function isEnclosure(): bool
    {
        return $this->enclosure;
    }

    public function setEnclosure(bool $enclosure): void
    {
        $this->enclosure = $enclosure;
    }

    public function getPubdate(): \DateTime
    {
        return $this->pubdate;
    }

    public function setPubdate(\DateTime $pubdate): void
    {
        $this->pubdate = $pubdate;
    }

    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeywords(array|Collection|string $keywords, $entityManager, $podcastKeywordsRepository): void
    {
        $slugger = new SlugGenerator();

        if (is_string($keywords)) {
            $alias = $slugger->generate($keywords);
            $item = $podcastKeywordsRepository->findOneBy(['alias' => $alias]);

            if (!$item) {
                $keywordEntity = new PodcastKeywords();
                $keywordEntity->setName($keywords);
                $keywordEntity->setAlias($alias);
                $this->keywords->add($keywordEntity);
                $entityManager->persist($keywordEntity);
                $entityManager->flush();
            }
        } elseif (is_array($keywords) || $keywords instanceof Collection) {
            foreach ($keywords as $keyword) {
                if (is_string($keyword)) {
                    $alias = $slugger->generate($keyword);
                    $item = $podcastKeywordsRepository->findOneBy(['alias' => $alias]);

                    if (!$item) {
                        $keywordEntity = new PodcastKeywords();
                        $keywordEntity->setName($keyword);
                        $keywordEntity->setAlias($alias);
                        $this->keywords->add($keywordEntity);
                        $entityManager->persist($keywordEntity);
                        $entityManager->flush();
                    }
                } elseif ($keyword instanceof PodcastKeywords) {
                    $this->keywords->add($keyword);
                }
            }
        }


    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getFolgentyp(): string
    {
        return $this->folgentyp;
    }

    public function setFolgentyp(string $folgentyp): void
    {
        $this->folgentyp = $folgentyp;
    }




    public function getThema(): Collection
    {
        return $this->thema;
    }

    public function setThema(Collection $thema): void
    {
        $this->thema = $thema;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }




}