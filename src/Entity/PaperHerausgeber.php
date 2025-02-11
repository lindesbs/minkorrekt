<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use lindesbs\minkorrekt\Repository\PaperHerausgeberRepository;

#[Entity(repositoryClass: PaperHerausgeberRepository::class)]
#[Table(name: 'mh_paper_herausgeber')]
class PaperHerausgeber
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 255)]
    private string $title;

    #[Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Column(type: 'integer')]
    private int $journal_id;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $language = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $screenshotSRC = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $screenshotFullpageSRC = null;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $editor = null;
}
