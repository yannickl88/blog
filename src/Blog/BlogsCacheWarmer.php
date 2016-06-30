<?php
namespace App\Blog;

use App\Git\RepositoryCrawler;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class BlogsCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var RepositoryCrawler
     */
    private $crawler;

    /**
     * @param RepositoryCrawler $crawler
     */
    public function __construct(RepositoryCrawler $crawler)
    {
        $this->crawler = $crawler;
    }
    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $this->crawler->updateAll();
    }
}
