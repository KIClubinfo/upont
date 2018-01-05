<?php

namespace App\Listener;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OnlineListener
{
    protected $manager;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
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
