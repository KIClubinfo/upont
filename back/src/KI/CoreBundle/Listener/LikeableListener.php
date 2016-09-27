<?php

namespace KI\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\CoreBundle\Entity\Comment;
use KI\CoreBundle\Entity\Likeable;
use KI\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LikeableListener
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($token = $this->tokenStorage->getToken()) {
            $user   = $token->getUser();
            $entity = $args->getEntity();

            if (($entity instanceof Likeable || $entity instanceof Comment) && $user instanceof User) {
                $entity->setLike($entity->isLiked($user));
                $entity->setDislike($entity->isDisliked($user));
            }
        }
    }
}
