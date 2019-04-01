<?php

namespace App\Listener;

use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RateLimitGenerateKeyListener
{
    protected $tokenStorage;

    /**
     * @param $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param GenerateKeyEvent $event
     */
    public function onGenerateKey(GenerateKeyEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        $event->addToKey($token->getUsername());
    }
}