<?php

namespace KI\PublicationBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use KI\PublicationBundle\Entity\Event;
use KI\UserBundle\Entity\User;

class EventLoadListener
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Event) {
            $token = $this->tokenStorage->getToken();
            $user = $token ? $token->getUser() : null;

            if ($user instanceof User) {
                $entity->setAttend($entity->isAttended($user));
                $entity->setPookie($entity->isHidden($user));
            }
        }
    }
}
