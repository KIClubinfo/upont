<?php

namespace KI\CoreBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceListener
{
    protected $maintenanceLock;

    public function __construct($maintenanceLock)
    {
        $this->maintenanceLock = $maintenanceLock;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = file_exists($this->maintenanceLock);
        $unlock = preg_match('#/maintenance$#', $event->getRequest()->getRequestUri());

        // Si la maintenance est activée et qu'on n'essaye pas de la débloquer
        if ($maintenance && !$unlock) {
            $content = array(
                'code' => 503,
                'message' => 'Service actuellement indisponible. Une maintenance est en cours.'
            );

            // Durée de la maintenance
            $until = file_get_contents($this->maintenanceLock);
            if ($until !== '') {
                $content['until'] = (int)$until;
            }

            $event->setResponse(new Response(json_encode($content), 503));
            $event->stopPropagation();
        }
    }
}
