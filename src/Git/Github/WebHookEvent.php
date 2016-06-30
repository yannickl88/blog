<?php
namespace App\Git\Github;

use App\Git\Repository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Webhook event wrapper.
 *
 * @see https://developer.github.com/webhooks/
 */
class WebHookEvent
{
    /**
     * @var string
     */
    private $event;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * Note: an InvalidSignatureException is thrown when the event was not
     * correctly signed.
     *
     * @param Request $request
     * @throws \App\Git\Github\Exception\InvalidSignatureException
     */
    public function __construct(Request $request)
    {
        $signature = $request->headers->get('X-Hub-Signature');
        $body      = $request->getContent();

        // validate the event
        WebHookEventValidator::validate($body, $signature, $request->server->get('GITHUB_SECRET'));

        $data = json_decode($body, true);

        $this->repository = new Repository(
            $data['repository']['full_name'],
            $data['repository']['clone_url']
        );
        $this->event      = $request->headers->get('X-GitHub-Event');
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
