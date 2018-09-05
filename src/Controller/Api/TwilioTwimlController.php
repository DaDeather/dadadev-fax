<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twilio\TwiML\FaxResponse;

/**
 * @Route("/api/twilio/twiml")
 */
class TwilioTwimlController extends AbstractController
{
    /**
     * @Route("/fax-receive")
     *
     * @return Response
     */
    public function receiveFax(): Response
    {
        $response = new FaxResponse();
        $response->receive([
            'action' => $this->generateUrl('app_api_twilio_receivefax', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'pageSize' => 'a4',
        ]);

        return new Response((string) $response);
    }
}
