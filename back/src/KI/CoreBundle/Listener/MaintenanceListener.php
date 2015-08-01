<?php

namespace KI\UpontBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $path = $this->container->get('kernel')->getRootDir().$this->container->getParameter('upont_maintenance_lock');
        $maintenance = file_exists($path);
        $unlock = preg_match('#/maintenance$#', $event->getRequest()->getRequestUri());

        // Si la maintenance est activée et qu'on n'essaye pas de la débloquer
        if ($maintenance && !$unlock) {
            $content = array('code' => 503, 'message' => 'Service actuellement indisponible. Une maintenance est en cours.');
            
            // Durée de la maintenance
            $until = file_get_contents($path);
            if ($until !== '')
                $content['until'] = (int)$until;
           
            $event->setResponse(new Response(json_encode($content), 503));
            $event->stopPropagation();
        }
    }
}
