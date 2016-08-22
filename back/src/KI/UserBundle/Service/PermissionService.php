<?php

namespace KI\UserBundle\Service;

use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\Club;
use KI\UserBundle\Entity\User;

class PermissionService
{
    protected $clubUserRepository;

    public function __construct(EntityRepository $clubUserRepository)
    {
        $this->clubUserRepository = $clubUserRepository;
    }

    /**
     * Indique si un utilisateur est membre d'un club ou non
     * @return boolean
     */
    public function isClubMember(User $user, Club $club)
    {
        $clubUser = $this->clubUserRepository->findOneBy([
            'user' => $user,
            'club' => $club
        ]);

        return (bool)$clubUser;
    }
}
