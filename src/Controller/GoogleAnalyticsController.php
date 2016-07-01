<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.google_analytics")
 */
class GoogleAnalyticsController
{
    /**
     * @Template("/ga_embed.html.twig")
     */
    public function embedAction(Request $request)
    {
        return [
            'ga_code' => $request->server->get('GA_CODE'),
        ];
    }
}
