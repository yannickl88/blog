<?php
namespace Yannickl88\Blog\Entity;

class Blog implements \JsonSerializable
{
    /**
     * @var Author
     */
    private $author;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var bool
     */
    private $is_draft;

    /**
     * @var array
     */
    private $tags;

    /**
     * @param Author    $author
     * @param \DateTime $date
     * @param string    $title
     * @param string    $file
     * @param string    $slug
     * @param bool      $draft
     * @param array     $tags
     */
    public function __construct(Author $author, \DateTime $date, $title, $file, $slug, $draft, array $tags = [])
    {
        $this->author   = $author;
        $this->date     = $date;
        $this->title    = $title;
        $this->file     = $file;
        $this->slug     = $slug;
        $this->is_draft = $draft;
        $this->tags     = $tags;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->file);
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return '/post/' . $this->slug;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->is_draft;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'author'   => $this->author->getUuid(),
            'date'     => $this->date->format('c'),
            'title'    => $this->title,
            'file'     => $this->file,
            'slug'     => $this->slug,
            'is_draft' => $this->is_draft,
            'tags'     => $this->tags,
        ];
    }
}
