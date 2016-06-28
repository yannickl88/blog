<?php
namespace Yannickl88\Blog;

use Yannickl88\Blog\Entity\Author;
use Yannickl88\Blog\Entity\Blog;

class Blogger
{
    private $authors = [];
    private $blogs   = [];

    private function __construct() {}

    /**
     * @param string $data_file
     * @return Blogger
     */
    public static function load($data_file)
    {
        $blog = new Blogger();

        if (!file_exists($data_file)) {
            return $blog;
        }
        $data = [];
        $files = json_decode(file_get_contents($data_file), true);

        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $data = array_merge($data, json_decode(file_get_contents($file), true));
        }

        foreach ($data['authors'] as $author_data) {
            $blog->authors[$author_data['uuid']] = new Author(
                $author_data['uuid'],
                $author_data['name'],
                $author_data['email'],
                $author_data['bio']
            );
        }

        foreach ($data['blogs'] as $blog_data) {
            $blog->blogs[$blog_data['slug']] = new Blog(
                $blog->authors[$blog_data['author']],
                new \DateTime($blog_data['date']),
                $blog_data['title'],
                $blog_data['file'],
                $blog_data['slug'],
                $blog_data['is_draft'],
                $blog_data['tags']
            );
        }

        uasort($blog->blogs, function (Blog $a, Blog $b) {
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }
            return $a->getDate() > $b->getDate() ? -1 : 1;
        });

        return $blog;
    }

    /**
     * @return Blog[]
     */
    public function getBlogs()
    {
        $now = new \DateTime();

        return array_filter($this->blogs, function (Blog $blog) use ($now) {
            return !$blog->isDraft() && $blog->getDate() < $now;
        });
    }

    /**
     * @param string $name
     * @return Blog
     */
    public function getBlog($name)
    {
        return $this->blogs[$name];
    }

    /**
     * @param Author $author
     * @return Blog[]
     */
    public function getBlogsForAuthor(Author $author)
    {
        $now = new \DateTime();

        return array_filter($this->blogs, function (Blog $blog) use ($author, $now) {
            return $blog->getAuthor() === $author && !$blog->isDraft() && $blog->getDate() < $now;
        });
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasBlog($name)
    {
        return isset($this->blogs[$name]);
    }
}

