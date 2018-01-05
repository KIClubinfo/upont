<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Achievement;
use App\Entity\AchievementUser;

class AchievementUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
