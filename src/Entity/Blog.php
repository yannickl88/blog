<?php
namespace Yannickl88\Blog\Entity;

class Blog
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
    private $url;

    /**
     * @var bool
     */
    private $is_draft;

    /**
     * @param Author    $author
     * @param \DateTime $date
     * @param string    $title
     * @param string    $file
     * @param string    $url
     * @param bool      $draft
     */
    public function __construct(Author $author, \DateTime $date, $title, $file, $url, $draft)
    {
        $this->author   = $author;
        $this->date     = $date;
        $this->title    = $title;
        $this->file     = $file;
        $this->url      = $url;
        $this->is_draft = $draft;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->is_draft;
    }
}
