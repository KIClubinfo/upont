<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Repository\ResourceRepository;
use App\Entity\PonthubFile;
use App\Entity\PonthubFileUser;
use App\Entity\Serie;
use App\Entity\User;

class PonthubFileUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PonthubFileUser::class);
    }

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
                    App:Episode episode,
                    App:PonthubFileUser pfu
                    WHERE pfu.file = episode
                    AND episode.serie = :serie
                    ')
            ->setParameter('serie', $serie)
            ->getSingleScalarResult();
    }
}
