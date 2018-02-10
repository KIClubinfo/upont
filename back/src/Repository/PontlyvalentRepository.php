<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\Pontlyvalent;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PontlyvalentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pontlyvalent::class);
    }

    /**
     * @param User $target
     * @param User $author
     * @return Pontlyvalent
     */
    public function getPontlyvalent(User $target, User $author)
    {
        $pontlyvent = $this->findBy([
            'target' => $target,
            'author' => $author
        ]);

        return $pontlyvent;
    }
}
