<?php
namespace App\Controller;

use App\Blog\BlogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route(service="app.controller.rss")
 */
class RssController
{
    /**
     * @var BlogRepository
     */
    private $blog_repository;

    public function __construct(BlogRepository $blog_repository)
    {
        $this->blog_repository = $blog_repository;
    }

    /**
     * @Route("/rss", name="app.rss")
     * @Template("rss.xml.twig")
     */
    public function indexAction()
    {
        return [
            'blogs' => $this->blog_repository->getBlogs()
        ];
    }
}
