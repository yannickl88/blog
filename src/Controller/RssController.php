<?php

namespace App\Controller;

use App\Blog\BlogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @Route(service="app.controller.rss")
 */
class RssController
{
    /**
     * @var BlogRepository
     */
    private $blogRepository;

    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct(BlogRepository $blogRepository, EngineInterface $templating)
    {
        $this->blogRepository = $blogRepository;
        $this->templating      = $templating;
    }

    /**
     * @Route("/rss", name="app.rss")
     */
    public function indexAction()
    {
        return new Response($this->templating->render(
            'rss.xml.twig',
            ['blogs' => $this->blogRepository->getBlogs()]
        ), 200, ['Content-Type' => 'application/rss+xml']);
    }
}
