<?php

namespace App\Controller;

use App\Blog\BlogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route(service="app.controller.index")
 */
class IndexController
{
    /**
     * @var BlogRepository
     */
    private $blog_repository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blog_repository = $blogRepository;
    }

    /**
     * @Route("/", name="app.index")
     * @Template("index.html.twig")
     */
    public function indexAction()
    {
        return [
            'blogs' => $this->blog_repository->getBlogs(),
        ];
    }
}
