<?php

namespace KI\PublicationBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use KI\PublicationBundle\Entity\Event;

class EventListener
{
    protected $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $token = $tokenStorage->getToken();
        $this->user = $token ? $token->getUser() : null;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Event) {
            $entity->setAttend($entity->isAttended($this->user));
            $entity->setPookie($entity->isHidden($this->user));
        }
    }
}
