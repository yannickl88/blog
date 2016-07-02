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
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/", name="app.index")
     * @Template("index.html.twig")
     */
    public function indexAction()
    {
        return [
            'blogs' => $this->blogRepository->getBlogs(),
        ];
    }
}
