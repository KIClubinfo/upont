<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\User;
use App\Repository\ClubUserRepository;

class PermissionService
{
    protected $clubUserRepository;

    public function __construct(ClubUserRepository $clubUserRepository)
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
