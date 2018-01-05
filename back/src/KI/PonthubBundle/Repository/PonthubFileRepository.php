<?php
namespace KI\PonthubBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use KI\PonthubBundle\Entity\PonthubFile;

class PonthubFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PonthubFile::class);
    }
}
