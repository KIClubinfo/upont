<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use KI\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;

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

        $user = $session->getUser();
        if (!$user instanceof User) {
            return;
        }

        $user->setLastConnect(time());
        $this->manager->flush();
    }
}
