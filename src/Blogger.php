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
        $data = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($data_file));

        foreach ($data['authors'] as $name => $author_data) {
            $blog->authors[$name] = new Author(
                $author_data['name'],
                $author_data['email'],
                __DIR__ . '/../authors/' . $name . '.md'
            );
        }

        foreach ($data['blogs'] as $name => $blog_data) {
            $blog->blogs[$name] = new Blog(
                $blog->authors[$blog_data['author']],
                new \DateTime('@' . $blog_data['date']),
                $blog_data['title'],
                __DIR__ . '/../blogs/' . $name . '.md',
                '/post/' . $name,
                $blog_data['draft']
            );
        }

        uasort($blog->blogs, function (Blog $a, Blog $b) {
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }
            return $a->getDate() < $b->getDate() ? -1 : 1;
        });

        return $blog;
    }

    /**
     * @return Blog[]
     */
    public function getBlogs()
    {
        return array_filter($this->blogs, function (Blog $blog) {
            return !$blog->isDraft();
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
        return array_filter($this->blogs, function (Blog $blog) use ($author) {
            return $blog->getAuthor() === $author && !$blog->isDraft();
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

