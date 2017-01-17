<?php

namespace KI\UserBundle\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        // Le token expire une semaine plus tard à 2h du matin
        $expiration = new \DateTime('+7 day');
        $expiration->setTime(2, 0, 0);

        $payload = $event->getData();
        $payload['ip'] = $request->getClientIp();
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }
}
