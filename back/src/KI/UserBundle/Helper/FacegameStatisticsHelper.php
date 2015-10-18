<?php

namespace KI\UserBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\User;


// Valide les formulaires pour une entité et affiche la réponse à la demande
class FacegameStatisticsHelper
{
    protected $manager;
    protected $repository;

    public function __construct(EntityManager $manager, EntityRepository $repository)
    {
        $this->manager    = $manager;
        $this->repository = $repository;
    }

    /**
     *  Retourne des statistiques globales sur toutes les parties
     *  @return array
     */
    public function globalStatistics()
    {
        return array(
            'totalNormal'        => $this->countNumberGamesNormal(),
            'totalHardcore'      => $this->countNumberGamesHardcore(),
            'normalHighscores'   => $this->getNormalHighscores(),
            'hardcoreHighscores' => $this->getHardcoreHighscores(),
        );
    }

    protected function countNumberGamesNormal()
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore <> 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    protected function countNumberGamesHardcore()
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore = 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    protected function getNormalHighscores()
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore <> 1')
            ->andWhere('o.duration <> 0')
            ->groupBy('o.user')
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;
        $facegames = $qb->getQuery()->getResult();
        $return = array();

        foreach ($facegames as $facegame) {
            $return[] = array(
                'name' => $facegame->getUser()->getFirstName().' '.$facegame->getUser()->getLastName(),
                'promo' => $facegame->getUser()->getPromo(),
                'duration' => $facegame->getDuration(),
                'date' => $facegame->getDate(),
            );
        }
        return $return;
    }

    protected function getHardcoreHighscores()
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore = 1')
            ->andWhere('o.duration <> 0')
            ->groupBy('o.user')
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;
        $facegames = $qb->getQuery()->getResult();
        $return = array();

        foreach ($facegames as $facegame) {
            $return[] = array(
                'name' => $facegame->getUser()->getFirstName().' '.$facegame->getUser()->getLastName(),
                'promo' => $facegame->getUser()->getPromo(),
                'duration' => $facegame->getDuration(),
                'date' => $facegame->getDate(),
            );
        }
        return $return;
    }

    /**
     *  Retourne des statistiques d'un utilisateur
     *  @param  User $user
     *  @return array
     */
    public function userStatistics(User $user)
    {
        return array(
            'totalNormal'        => $this->countUserGames($user, 0),
            'totalHardcore'      => $this->countUserGames($user, 1),
            'normalHighscores'   => $this->getUserHighscores($user, 0),
            'hardcoreHighscores' => $this->getUserHighscores($user, 1),
        );
    }

    protected function countUserGames($user, $mode)
    {
        $qb = $this->repository->createQueryBuilder('o');
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

    protected function getUserHighscores($user, $mode)
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
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
