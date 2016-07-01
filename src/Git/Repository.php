<?php

namespace App\Git;

/**
 * Repository data wrapper.
 */
class Repository
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $master;

    /**
     * @param string $name
     * @param string $url
     */
    public function __construct($name, $url)
    {
        $this->name = str_replace('/', '_', strtolower($name));
        $this->url = $url;
    }

    /**
     * Return name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return git URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
