<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\Achievement;

class AchievementUserRepository extends ResourceRepository
{
    public function getOwnedByCount(Achievement $achievement)
    {
        return $this->createQueryBuilder('au')
            ->select('COUNT(au)')
            ->where('au.achievement = :achievement')
            ->setParameter('achievement', $achievement)
            ->getQuery()
            ->getSingleScalarResult();
    }
}


