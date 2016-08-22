<?php
namespace KI\PonthubBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\PonthubBundle\Entity\PonthubFile;
use KI\PonthubBundle\Entity\Serie;
use KI\UserBundle\Entity\User;

class PonthubFileUserRepository extends ResourceRepository
{
    public function hasBeenDownloadedBy(PonthubFile $file, User $user)
    {
        return $this->createQueryBuilder('pfu')
            ->select('COUNT(pfu.id)')
            ->where('pfu.user = :user')
            ->andWhere('pfu.file = :file')
            ->setParameter('file', $file)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function getSerieDownloads(Serie $serie)
    {
//        return $this->createQueryBuilder('pfu')
//            ->select('COUNT(pfu.id)')
//            ->leftJoin('pfu.file', 'e')
//            ->where('e.serie = :serie')
//            ->setParameter('serie', $serie)
//            ->getQuery()
//            ->getSingleScalarResult();

        return $this->getEntityManager()->createQuery('SELECT COUNT(pfu.id) FROM
                    KIPonthubBundle:Episode episode,
                    KIPonthubBundle:PonthubFileUser pfu
                    WHERE pfu.file = episode
                    AND episode.serie = :serie
                    ')
            ->setParameter('serie', $serie)
            ->getSingleScalarResult();
    }
}
