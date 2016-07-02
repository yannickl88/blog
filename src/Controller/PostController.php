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
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * @Route("/post/{name}", name="app.post")
     * @Template("post.html.twig")
     */
    public function indexAction($name)
    {
        if (null === ($blog = $this->blogRepository->getBlog($name))) {
            throw new NotFoundHttpException();
        }

        return [
            'blog' => $blog,
            'related' => $this->blogRepository->getBlogsForAuthor($blog->getAuthor()),
        ];
    }

    /**
     * @Route("/author/{name}", name="app.author")
     * @Template("author.html.twig")
     */
    public function authorAction($name)
    {
        if (null === ($author = $this->blogRepository->getAuthorByName($name))) {
            throw new NotFoundHttpException();
        }

        return [
            'author' => $author,
            'blogs' => $this->blogRepository->getBlogsForAuthor($author)
        ];
    }

    /**
     * @Route("/tag/{tag}", name="app.tag")
     * @Template("tag.html.twig")
     */
    public function tagAction($tag)
    {
        return [
            'tag_blogs' => $this->blogRepository->getBlogsForTag($tag),
            'blogs' => $this->blogRepository->getBlogs(),
        ];
    }
}
