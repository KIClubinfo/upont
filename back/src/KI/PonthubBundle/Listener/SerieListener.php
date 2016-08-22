<?php

namespace KI\PonthubBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\PonthubBundle\Entity\Serie;

class SerieListener
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Serie) {
            $entity->setDownloads(
                $args->getEntityManager()
                    ->getRepository('KIPonthubBundle:PonthubFileUser')
                    ->getSerieDownloads($entity)
            );
        }
    }
}
