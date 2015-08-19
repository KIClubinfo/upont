<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class OnlineListener
{
    protected $manager;
    protected $securityContext;

    public function __construct(EntityManager $manager, SecurityContext $securityContext)
    {
        $this->manager         = $manager;
        $this->securityContext = $securityContext;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $this->securityContext->getToken();
        if (!method_exists($session, 'getUser')) {
            return;
        }

        $session->getUser()->setLastConnect(time());
        $this->manager->flush();
    }
}
