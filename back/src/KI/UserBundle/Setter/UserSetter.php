<?php

namespace KI\UserBundle\Setter;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserSetter
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $token = $this->tokenStorage->getToken();

        if (property_exists($entity, 'autoSetUser') && $token !== null && $entity->getUser() === null) {
            $entity->setUser($token->getUser());
        }
        $entityManager = $args->getEntityManager();
    }
}
