<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Club;
use App\Entity\ClubUser;
use App\Entity\User;

class ClubUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClubUser::class);
    }

    public function getUserBelowInClubWithPromo(Club $club, $promo, $priority)
    {
        return $this->getEntityManager()->createQuery('SELECT cu
                FROM App:ClubUser cu,
                App:User user
                WHERE cu.club = :club
	            AND cu.user = user
                AND user.promo = :promo
                AND cu.priority > :priority
                ORDER BY cu.priority ASC')
        ->setParameter('club', $club)
        ->setParameter('priority', $priority)
        ->setParameter('promo', $promo)
        ->setMaxResults(1)
        ->getSingleResult();
    }

    public function getUserAboveInClubWithPromo(Club $club, $promo, $priority)
    {
        return $this->getEntityManager()->createQuery('SELECT cu
                FROM App:ClubUser cu,
                App:User user
                WHERE cu.club = :club
	            AND cu.user = user
                AND user.promo = :promo
                AND cu.priority < :priority
                ORDER BY cu.priority DESC')
            ->setParameter('club', $club)
            ->setParameter('priority', $priority)
            ->setParameter('promo', $promo)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    public function getCountUsersInClubWithPromo(Club $club, $promo)
    {
        return $this->getEntityManager()->createQuery('SELECT count(cu)
                FROM App:ClubUser cu,
                App:User user
                WHERE cu.club = :club
                AND cu.user = user
                AND user.promo = :promo')
            ->setParameter('club', $club)
            ->setParameter('promo', $promo)
            ->getSingleScalarResult();
    }
}
