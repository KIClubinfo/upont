<?php

namespace KI\UpontBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        if (!($request = $event->getRequest())) {
            return;
        }
        //Le token expire une semaine plus tard à 2h du matin
        $expiration = new \DateTime('+7 day');
        $expiration->setTime(2, 0, 0);

        $payload       = $event->getData();
        $payload['ip'] = $request->getClientIp();
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }
}