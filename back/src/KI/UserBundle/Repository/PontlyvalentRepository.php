<?php
namespace KI\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use KI\UserBundle\Entity\Pontlyvalent;
use KI\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PontlyvalentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
