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

        if (method_exists($entity, 'getAutoSetUser') && $token !== null) {
            $suffix = ucfirst($entity->getAutoSetUser());
            $getter = 'get'.$suffix;
            $setter = 'set'.$suffix;

            if ($entity->$getter() === null) {
                $entity->$setter($token->getUser());
            }
        }
        $entityManager = $args->getEntityManager();
    }
}
