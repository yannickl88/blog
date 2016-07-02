<?php

namespace App\Blog;

use App\Entity\Author;
use App\Entity\Blog;

/**
 * Repository class for all blog posts.
 */
class BlogRepository
{
    private $data_file;
    private $initialized = false;
    private $authors = [];
    private $blogs = [];

    /**
     * @param string $data_file
     */
    public function __construct($data_file)
    {
        $this->data_file = $data_file;
    }

    /**
     * Initialize all the data from cache.
     */
    private function load()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        if (!file_exists($this->data_file)) {
            return;
        }
        $data = [];
        $files = json_decode(file_get_contents($this->data_file), true);

        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $data = array_merge_recursive($data, json_decode(file_get_contents($file), true));
        }

        foreach ($data['authors'] as $author_data) {
            $this->authors[$author_data['uuid']] = new Author(
                $author_data['uuid'],
                $author_data['name'],
                $author_data['email'],
                $author_data['bio'],
                $author_data['urls']
            );
        }

        foreach ($data['blogs'] as $blog_data) {
            $this->blogs[$blog_data['slug']] = new Blog(
                $this->authors[$blog_data['author']],
                new \DateTime($blog_data['date']),
                $blog_data['title'],
                $blog_data['file'],
                $blog_data['slug'],
                $blog_data['is_draft'],
                $blog_data['tags']
            );
        }

        uasort($this->blogs, function (Blog $a, Blog $b) {
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }

            return $a->getDate() > $b->getDate() ? -1 : 1;
        });
    }

    /**
     * @return Blog[]
     */
    public function getBlogs()
    {
        $this->load();
        $now = new \DateTime();

        return array_filter($this->blogs, function (Blog $blog) use ($now) {
            return !$blog->isDraft() && $blog->getDate() < $now;
        });
    }

    /**
     * @param string $name
     *
     * @return Blog|null
     */
    public function getBlog($name)
    {
        $this->load();

        return $this->hasBlog($name) ? $this->blogs[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return Author|null
     */
    public function getAuthorByName($name)
    {
        $this->load();

        return current(array_filter($this->authors, function (Author $author) use ($name) {
            return strtolower($author->getShortName()) === strtolower($name);
        })) ? : null;
    }

    /**
     * @param Author $author
     *
     * @return Blog[]
     */
    public function getBlogsForAuthor(Author $author)
    {
        $this->load();
        $now = new \DateTime();

        return array_filter($this->blogs, function (Blog $blog) use ($author, $now) {
            return $blog->getAuthor() === $author && !$blog->isDraft() && $blog->getDate() < $now;
        });
    }

    /**
     * @param string $tag
     *
     * @return Blog[]
     */
    public function getBlogsForTag($tag)
    {
        $this->load();
        $now = new \DateTime();

        return array_filter($this->blogs, function (Blog $blog) use ($tag, $now) {
            return in_array($tag, $blog->getTags()) && !$blog->isDraft() && $blog->getDate() < $now;
        });
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasBlog($name)
    {
        $this->load();

        return isset($this->blogs[$name]);
    }
}
