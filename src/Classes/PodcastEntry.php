<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Classes;

use DateTime;
use DOMNode;

class PodcastEntry
{

    private string $title;
    private string $subtitle;

    private string $episode;
    private string $summary;
    private string $link;
    private string $guid;

    private string $content;



    private bool $explicit;

    private array $keywords;
    private string $author;

    private int $duration;

    private string $description;
    private DateTime $pubDate;


    public function __construct(DOMNode $element)
    {
        $values=[];
        /** @var \DOMNodeList $nodelist */
        $nodelist = $element->childNodes;
        foreach($nodelist as $node) {

            if (strlen(trim($node->nodeValue))>0)
                $values[$node->nodeName] = $node->nodeValue;
        }

        $dt = new \DateTime($values['pubDate']);

        $this->setTitle($values['title']);
        $this->setDescription($values['description']);
        $this->setLink($values['link']);
        $this->setGuid($values['guid']);
        $this->setContent($values['content:encoded']);
        $this->setEpisode($values['itunes:episode']);
        $this->setSubtitle($values['itunes:subtitle']);
        $this->setSummary($values['itunes:summary']);
        $this->setExplicit($values['itunes:explicit'] === 'yes');

        if ((array_key_exists('itunes:keywords', $values)) && ($values['itunes:keywords']))
            $this->setKeywords(explode(',',$values['itunes:keywords']));


        $this->setAuthor($values['itunes:author']);


        if ((array_key_exists('itunes:duration', $values)) && ($values['itunes:duration']))
        $this->setDuration((int) $values['itunes:duration']);

        $this->setPubDate($dt);


    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getPubDate(): DateTime
    {
        return $this->pubDate;
    }

    /**
     * @param DateTime $pubDate
     */
    public function setPubDate(DateTime $pubDate): void
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string
     */
    public function getEpisode(): string
    {
        return $this->episode;
    }

    /**
     * @param string $episode
     */
    public function setEpisode(string $episode): void
    {
        $this->episode = $episode;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return bool
     */
    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    /**
     * @param bool $explicit
     */
    public function setExplicit(bool $explicit): void
    {
        $this->explicit = $explicit;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }





}