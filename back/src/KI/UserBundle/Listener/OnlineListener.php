<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use KI\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class OnlineListener
{
    protected $manager;
    protected $tokenStorage;

    public function __construct(EntityManager $manager, TokenStorage $tokenStorage)
    {
        $this->manager         = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest()
    {
        $session = $this->tokenStorage->getToken();
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
