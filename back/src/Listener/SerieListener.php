<?php

namespace App\Listener;

use App\Entity\PonthubFileUser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Serie;

class SerieListener
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Serie) {
            $entity->setDownloads(
                $args->getEntityManager()
                    ->getRepository(PonthubFileUser::class)
                    ->getSerieDownloads($entity)
            );
        }
    }
}
