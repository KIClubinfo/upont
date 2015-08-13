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
            'totalNormal'   => $this->countNumberGamesNormal(),
            'totalHardcore' => $this->countNumberGamesHardcore(),
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

    /**
     *  Retourne des statistiques d'un utilisateur
     *  @param  User $user
     *  @return array
     */
    public function userStatistics(User $user)
    {

    }
}
