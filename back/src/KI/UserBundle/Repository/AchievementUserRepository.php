<?php
namespace KI\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\AchievementUser;

class AchievementUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AchievementUser::class);
    }

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
