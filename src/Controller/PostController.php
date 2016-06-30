<?php
namespace App\Controller;

use App\Blog\BlogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service="app.controller.post")
 */
class PostController
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
     * @Route("/post/{name}", name="app.post")
     * @Template("post.html.twig")
     */
    public function indexAction($name)
    {
        if (null === ($blog = $this->blog_repository->getBlog($name))) {
            throw new NotFoundHttpException();
        }

        return [
            'blog'    => $blog,
            'related' => $this->blog_repository->getBlogsForAuthor($blog->getAuthor())
        ];
    }
}
