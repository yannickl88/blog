<?php

namespace App\Git;

/**
 * Repository data wrapper.
 */
class Repository
{
    private $name;
    private $url;

    public function __construct(string $name, string $url)
    {
        $this->name = str_replace('/', '_', strtolower($name));
        $this->url = $url;
    }

    /**
     * Returns the name of the repository.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the git URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
