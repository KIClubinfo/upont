<?php

namespace KI\CoreBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener
{
    protected $lockfilePath;

    public function __construct($lockfilePath)
    {
        $this->lockfilePath = $lockfilePath;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = file_exists($this->lockfilePath);
        $unlock = preg_match('/\/maintenance$/', $event->getRequest()->getRequestUri());

        // Si la maintenance est activée et qu'on n'essaye pas de la débloquer
        if ($maintenance && !$unlock) {
            $content = [
                'code' => 503,
                'message' => 'Service actuellement indisponible. Une maintenance est en cours.'
            ];

            // Durée de la maintenance
            $until = file_get_contents($this->lockfilePath);
            if ($until !== '') {
                $content['until'] = (int)$until;
            }

            $event->setResponse(new Response(json_encode($content), 503));
            $event->stopPropagation();
        }
    }
}
