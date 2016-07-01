<?php

namespace App\Controller;

use App\Git\GitFetchException;
use App\Git\Github\Exception\InvalidSignatureException;
use App\Git\Github\WebHookEvent;
use App\Git\RepositoryCrawler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.github")
 */
class GithubController
{
    /**
     * @var RepositoryCrawler
     */
    private $crawler;

    /**
     * @param RepositoryCrawler $crawler
     */
    public function __construct(RepositoryCrawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @Route("/hook", name="app.github.hook")
     */
    public function hookAction(Request $request)
    {
        try {
            $event = new WebHookEvent($request);
        } catch (InvalidSignatureException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        switch ($event->getEvent()) {
            case 'ping':
                return new JsonResponse('PONG');
            case 'push':
                try {
                    $this->crawler->update($event->getRepository());
                } catch (GitFetchException $e) {
                    return new JsonResponse(['error' => $e->getMessage()], 500);
                }

                return new JsonResponse(true);
        }

        return new JsonResponse(['error' => 'Can only accept event "ping" and "push"'], 500);
    }
}
