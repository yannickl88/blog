<?php
namespace Yannickl88\Blog;

class Repository
{
    private $name;
    private $url;
    private $master;

    public function __construct($name, $url, $master)
    {
        $this->name   = str_replace('/', '_', strtolower($name));
        $this->url    = $url;
        $this->master = $master;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getMaster()
    {
        return $this->master;
    }
}