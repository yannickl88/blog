<?php
namespace Yannickl88\Blog;

use Symfony\Component\Yaml\Yaml;
use Yannickl88\Blog\Entity\Author;
use Yannickl88\Blog\Entity\Blog;

class RepositoryManager
{
    private $repositoriesLocation;
    private $cacheLocation;

    public function __construct($repositoriesLocation, $cacheLocation)
    {
        $this->repositoriesLocation = $repositoriesLocation;
        $this->cacheLocation = $cacheLocation;
    }
    
    public function update(Repository $repository)
    {
        $location = $this->repositoriesLocation . '/' . $repository->getName();
        $master   = $repository->getMaster();

        if (!file_exists($location)) {
            $url = $repository->getUrl();

            `git clone $url $location`;
            return $this->index($location, $repository);
        }

        $cwd = getcwd();

        // cd to git repo
        chdir($location);

        `git fetch`;
        `git reset --hard {$master}`;

        // cd back
        chdir($cwd);

        return $this->index($location, $repository);
    }

    private function index($location, Repository $repository)
    {
        // get the index file
        $blog = Yaml::parse(file_get_contents($location . '/blog.yaml'));
        $data = [
            'blogs' => []
        ];

        $author = new Author(
            $this->generateUUID($repository->getUrl()),
            $blog['author']['name'],
            $blog['author']['email'],
            $location . '/' . $blog['settings']['introduction']
        );
        $data['authors'] = [$author];

        // parse blog posts
        $published_posts = $location . '/' . $blog['settings']['published'];
        $draft_posts     = $location . '/' . $blog['settings']['drafts'];

        foreach (glob($published_posts . '/*.md') as $file) {
            list($date, $title, $slug, $tags) = $this->extractData($file);

            $data['blogs'][] = new Blog($author, $date, $title, $file, $slug, false, $tags);
        }

        foreach (glob($draft_posts . '/*.md') as $file) {
            list($date, $title, $slug, $tags) = $this->extractData($file);

            $data['blogs'][] = new Blog($author, $date, $title, $file, $slug, true, $tags);
        }

        $file = $this->cacheLocation . '/' . $repository->getName() . '.json';
        $root_file = $this->cacheLocation . '/blogs.json';
        $files = array_unique(array_merge(file_exists($root_file) ? json_decode(file_get_contents($root_file), true) : [], [$file]));

        file_put_contents($file, json_encode($data));
        file_put_contents($root_file, json_encode($files));
    }

    private function generateUUID($string) {
        $data = substr(md5($string), 0, 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function extractData($file)
    {
        $date = new \DateTime();
        $title = basename($file, '.md');
        $slug = $title;
        $tags = [];

        foreach (file($file) as $line) {
            if (strpos($line, '[//]: # (') !== 0) {
                continue;
            }

            $data = substr(trim($line), 9);
            $key = substr($data, 0, strpos($data, ':'));
            $value = trim(substr($data, strlen($key) + 1, -1));

            switch ($key) {
                case 'TITLE':
                    $title = $value;
                    break;
                case 'DATE':
                    $date = new \DateTime($value);
                    break;
                case 'TAGS':
                    $tags = array_map('trim', explode(',', $value));
                    break;
            }
        }

        return [
            $date, $title, $slug, $tags
        ];
    }
}
