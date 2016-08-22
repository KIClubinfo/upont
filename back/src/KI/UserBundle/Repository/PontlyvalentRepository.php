<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\Pontlyvalent;
use KI\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PontlyvalentRepository extends ResourceRepository
{
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
