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

    public function countNumberGamesNormal()
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore <> 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countNumberGamesHardcore()
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore = 1')
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getNormalHighscores()
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore <> 1')
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

    public function getHardcoreHighscores()
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o')
            ->where('o.hardcore = 1')
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
            'totalNormal'        => $this->countUserGamesNormal($user),
            'totalHardcore'      => $this->countUserGamesHardcore($user),
            'normalHighscores'   => $this->getUserNormalHighscores($user),
            'hardcoreHighscores' => $this->getUserHardcoreHighscores($user),
        );
    }

    public function countUserGamesNormal($user)
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore <> 1')
            ->andWhere('o.user = ?0')
            ->setParameter(0, $user)
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countUserGamesHardcore($user)
    {
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.hardcore = 1')
            ->andWhere('o.user = ?0')
            ->setParameter(0, $user)
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getUserNormalHighscores($user)
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o.duration', 'o.date')
            ->where('o.hardcore <> 1')
            ->andWhere('o.user = ?0')
            ->setParameter(0, $user)
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getUserHardcoreHighscores($user)
    {
        $maxResults = 10;
        $qb = $this->repository->createQueryBuilder('o');
        $qb
            ->select('o.duration', 'o.date')
            ->where('o.hardcore = 1')
            ->andWhere('o.user = ?0')
            ->setParameter(0, $user)
            ->orderBy('o.duration', 'ASC')
            ->setMaxResults($maxResults)
        ;

        return $qb->getQuery()->getResult();
    }
}
