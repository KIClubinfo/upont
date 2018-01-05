<?php

namespace App\Listener;

use App\Entity\PonthubFileUser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\PonthubFile;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
                $entity->setDownloaded(
                    $args->getEntityManager()
                        ->getRepository(PonthubFileUser::class)
                        ->hasBeenDownloadedBy($entity, $user)
                );
            }
        }
    }
}
