<?php
namespace KI\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use KI\UserBundle\Entity\Facegame;
use KI\UserBundle\Entity\User;

class FacegameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facegame::class);
    }

    public function getNormalGamesCount()
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore <> 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getHardcoreGamesCount()
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore = 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getNormalHighscores()
    {
        $maxResults = 10;
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore <> 1')
            ->andWhere('o.duration <> 0')
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;
        $facegames = $qb->getQuery()->getResult();
        $return = [];

        foreach ($facegames as $facegame) {
            $return[] = [
                'name' => $facegame->getUser()->getFirstName().' '.$facegame->getUser()->getLastName(),
                'promo' => $facegame->getUser()->getPromo(),
                'duration' => $facegame->getDuration(),
                'date' => $facegame->getDate(),
            ];
        }
        return $return;
    }

    public function getHardcoreHighscores()
    {
        $maxResults = 10;
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore = 1')
            ->andWhere('o.duration <> 0')
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;
        $facegames = $qb->getQuery()->getResult();
        $return = [];

        foreach ($facegames as $facegame) {
            $return[] = [
                'name' => $facegame->getUser()->getFirstName().' '.$facegame->getUser()->getLastName(),
                'promo' => $facegame->getUser()->getPromo(),
                'duration' => $facegame->getDuration(),
                'date' => $facegame->getDate(),
            ];
        }
        return $return;
    }

    public function getUserGamesCount(User $user, $mode)
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore = :mode')
            ->andWhere('o.duration <> 0')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->setParameter('mode', $mode)
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getUserHighscores(User $user, $mode)
    {
        $maxResults = 10;
        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('o.duration', 'o.date')
            ->where('o.hardcore = :mode')
            ->andWhere('o.user = :user')
            ->andWhere('o.duration <> 0')
            ->setParameter('user', $user)
            ->setParameter('mode', $mode)
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;

        return $qb->getQuery()->getResult();
    }
}
