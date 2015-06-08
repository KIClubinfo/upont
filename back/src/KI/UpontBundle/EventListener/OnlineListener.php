<?php

namespace KI\UpontBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class OnlineListener extends ContainerAware
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $this->container->get('security.context')->getToken();
        if (!method_exists($session, 'getUser'))
            return;

        $manager = $this->container->get('doctrine')->getManager();
        $user = $session->getUser();
        $user->setLastConnect(time());
        $manager->flush();
    }
}
