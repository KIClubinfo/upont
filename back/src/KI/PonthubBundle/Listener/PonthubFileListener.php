<?php

namespace KI\PonthubBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use KI\PonthubBundle\Entity\PonthubFile;
use KI\UserBundle\Entity\User;

class PonthubFileListener
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

            if ($entity instanceof PonthubFile && $user instanceof User) {
                $entity->setDownloaded($entity->hasBeenDownloadedBy($user));
            }
        }
    }
}
