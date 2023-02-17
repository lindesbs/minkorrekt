<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Classes;

class PodcastEntry
{
    private string $title;

    private string $subtitle;

    private int $episode;

    private string $summary;

    private string $link;

    private string $guid;

    private string $content;

    private bool $explicit;

    private array $keywords;

    private string $author;

    private int $duration;

    private string $description;

    private \DateTime $dateTime;

    public function __construct(\DOMNode $domNode)
    {
        $values = [];
        /** @var \DOMNodeList $nodelist */
        $nodelist = $domNode->childNodes;

        foreach ($nodelist as $node) {
            if ('' !== trim($node->nodeValue)) {
                $values[$node->nodeName] = $node->nodeValue;
            }
        }

        $dateTime = new \DateTime($values['pubDate']);

        $this->setTitle($values['title']);
        $this->setDescription($values['description']);
        $this->setLink($values['link']);
        $this->setGuid($values['guid']);
        $this->setContent(str_replace('<br>', "\n", $values['content:encoded']));
        $this->setEpisode((int)$values['itunes:episode']);
        $this->setSubtitle($values['itunes:subtitle']);
        $this->setSummary($values['itunes:summary']);
        $this->setExplicit('yes' === $values['itunes:explicit']);

        if (\array_key_exists('itunes:keywords', $values) && ($values['itunes:keywords'])) {
            $this->setKeywords(explode(',', $values['itunes:keywords']));
        }

        $this->setAuthor($values['itunes:author']);

        if (\array_key_exists('itunes:duration', $values) && ($values['itunes:duration'])) {
            $this->setDuration((int)$values['itunes:duration']);
        }

        $this->setPubDate($dateTime);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPubDate(): \DateTime
    {
        return $this->dateTime;
    }

    public function setPubDate(\DateTime $pubDate): void
    {
        $this->dateTime = $pubDate;
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

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    public function setExplicit(bool $explicit): void
    {
        $this->explicit = $explicit;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
