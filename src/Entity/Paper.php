<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use lindesbs\minkorrekt\Repository\PaperRepository;


#[Entity(repositoryClass: PaperRepository::class)]
#[Table(name: 'mh_paper')]
class Paper
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 255)]
    private string $title;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_title = null;

    #[Column(type: 'string', length: 255)]
    private string $alias;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $copyright = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $rights = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $rightsAgent = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $thePublisher = null;

    #[Column(type: 'boolean', nullable: true)]
    private ?bool $published = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $status = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $license = null;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTime $onlineAt = null;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTime $receivedAt = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $size = null;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTime $acceptedAt = null;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $doi = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $doiurl = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_springer_api_url = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $subjects = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $screenshotSRC = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $screenshotFullpageSRC = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $tlContentId = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $tlNewsId = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $price = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $paperType = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $language = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $twitter = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_firstpage = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_lastpage = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_article_type = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_pdf_url = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_fulltext_html_url = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $citation_issn = null;
            
}