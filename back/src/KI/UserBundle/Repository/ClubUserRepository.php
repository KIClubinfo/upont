<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\Club;
use KI\UserBundle\Entity\User;

class ClubUserRepository extends ResourceRepository
{
    public function getUserBelowInClubWithPromo(Club $club, $promo, $priority)
    {
        return $this->getEntityManager()->createQuery('SELECT cu
                FROM KIUserBundle:ClubUser cu,
                KIUserBundle:User user
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
                FROM KIUserBundle:ClubUser cu,
                KIUserBundle:User user
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
}


