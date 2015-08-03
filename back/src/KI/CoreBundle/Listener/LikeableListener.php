<?php

namespace KI\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use KI\CoreBundle\Entity\Likeable;

class LikeableListener
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args) { $this->loadLikes($args); }

    public function loadLikes(LifecycleEventArgs $args)
    {
        if ($token = $this->tokenStorage->getToken()) {
            $user = $token->getUser();
            $entity = $args->getEntity();

            if ($entity instanceof Likeable) {
                $entity->setLike($entity->isLiked($user));
                $entity->setDislike($entity->isDisliked($user));
            }
        }
    }
}
