<?php
namespace App\Git;

use App\Entity\Author;
use App\Entity\Blog;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * Crawler for the Git repository so we can use the information.
 */
class RepositoryCrawler
{
    /**
     * @var string
     */
    private $repositoriesLocation;

    /**
     * @var string
     */
    private $cacheDataFile;

    /**
     * @param string $repositoriesLocation
     * @param string $cacheDataFile
     */
    public function __construct($repositoriesLocation, $cacheDataFile)
    {
        $this->repositoriesLocation = $repositoriesLocation;
        $this->cacheDataFile        = $cacheDataFile;
    }

    /**
     * Update a given repository information.
     *
     * @param Repository $repository
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \App\Git\GitFetchException
     */
    public function update(Repository $repository)
    {
        $location = $this->repositoriesLocation . '/' . $repository->getName();
        $master   = $repository->getMaster();

        if (!file_exists($location)) {
            $url = $repository->getUrl();

            try {
                $process = new Process(sprintf('git clone %s %s', $url, $location));
                $process->mustRun();
            } catch (ProcessFailedException $e) {
                throw new GitFetchException(sprintf('Could not clone project "%s".', $url));
            }

            $this->index($location, $repository);
            return;
        }

        try {
            // fetch new information and update to newest master branch
            $process = new Process(sprintf('git fetch && git reset --hard %s', $master), $location);
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new GitFetchException(sprintf('Could not reset repository for "%s".', $location), null, $e);
        }

        $this->index($location, $repository);
    }

    /**
     * Index the location.
     *
     * @param string     $location
     * @param Repository $repository
     * @throws \App\Git\GitFetchException
     */
    private function index($location, Repository $repository)
    {
        if (!file_exists($location . '/blog.yaml')) {
            throw new GitFetchException(
                sprintf('No "blog.yaml" found in repository "%s" as "%s".', $repository->getUrl(), $location)
            );
        }

        // get the index file
        $blog = Yaml::parse(file_get_contents($location . '/blog.yaml'));
        $data = [
            'blogs' => []
        ];

        $author = new Author(
            $this->generateUuid($repository->getUrl()),
            $blog['author']['name'],
            $blog['author']['email'],
            $location . '/' . $blog['settings']['introduction']
        );
        $data['authors'] = [$author];

        // parse blog posts
        $publishedPosts = $location . '/' . $blog['settings']['published'];
        $draftPosts     = $location . '/' . $blog['settings']['drafts'];

        foreach (glob($publishedPosts . '/*.md') as $file) {
            list($date, $title, $slug, $tags) = $this->extractData($file);

            $data['blogs'][] = new Blog($author, $date, $title, $file, $slug, false, $tags);
        }

        foreach (glob($draftPosts . '/*.md') as $file) {
            list($date, $title, $slug, $tags) = $this->extractData($file);

            $data['blogs'][] = new Blog($author, $date, $title, $file, $slug, true, $tags);
        }

        $file = dirname($this->cacheDataFile) . '/' . $repository->getName() . '.json';
        $rootFile = $this->cacheDataFile;
        $files = array_unique(array_merge(file_exists($rootFile) ? json_decode(file_get_contents($rootFile), true) : [], [$file]));

        file_put_contents($file, json_encode($data));
        file_put_contents($rootFile, json_encode($files));
    }

    /**
     * Create UUID from a given string. This UUID will always be the same as
     * long as the input is the same.
     *
     * @param string $string
     * @return string
     */
    private function generateUuid($string) {
        $data = substr(md5($string), 0, 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Extract meta information from a blog post.
     *
     * Supported meta tags: TITLE, DATE and TAGS
     *
     * @param string $file
     * @return array
     */
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