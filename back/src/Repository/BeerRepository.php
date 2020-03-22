<?php

namespace App\Repository;

use App\Entity\Beer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BeerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Beer::class);
    }

    /**
     * Trie les bières par ordre décroissant de consommation
     * @return Beer[]
     */
    public function getBeerOrderedList()
    {
        return $this->getEntityManager()->createQuery('SELECT beer FROM
            App:Beer beer
            LEFT OUTER JOIN beer.transactions transac
            WHERE transac.user IS NOT NULL OR transac.id IS NULL
            GROUP BY beer.id
            ORDER BY COUNT(transac) DESC'
        )->getArrayResult();
    }
}
