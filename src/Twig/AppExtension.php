<?php

namespace App\Twig;

/**
 * App twig extensions.
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var \Parsedown
     */
    private $parser;

    /**
     * @param \Parsedown $parser
     */
    public function __construct(\Parsedown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdown', [$this, 'markdownFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Parse a string with Markdown.
     *
     * @param $string
     *
     * @return string
     */
    public function markdownFilter($string)
    {
        return $this->parser->text($string);
    }
}
