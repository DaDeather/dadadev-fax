<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonToRequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Get the data
        $request = $event->getRequest();

        // Check if its even a json request and if so proceed
        if ('json' === $request->getContentType()) {
            $content = $request->getContent();

            if (empty($content)) {
                return;
            }

            $jsonData = json_decode($content, true);
            if (null === $jsonData) {
                return;
            }

            $request->request->replace($jsonData);
        }
    }
}
