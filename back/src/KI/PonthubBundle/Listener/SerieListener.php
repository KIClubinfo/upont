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
                $args->getEntityManager()->createQuery('SELECT COUNT(pfu.id) FROM
                    KIPonthubBundle:Episode episode, 
                    KIPonthubBundle:PonthubFileUser pfu
                    WHERE pfu.file = episode
                    AND episode.serie = :serie
                    ')
                ->setParameter('serie', $entity)
                ->getSingleScalarResult()
            );
        }
    }
}
