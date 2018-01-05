<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TokenService
{
    protected $manager;
    protected $tokenStorage;

    public function __construct(EntityManager $manager, TokenStorage $tokenStorage)
    {
        $this->manager         = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    // Génère un token pour l'utilisateur
    // On n'utilise pas le JWT pour avoir un token plus personnalisable et moins long
    // (donc moins moche)
    public function getToken(User $user = null)
    {
        if ($user === null) {
            if ($result = $this->tokenStorage->getToken())
                $user = $result->getUser();
            else
                return;
        }


        if (empty($user->getToken())) {
            $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            //OHHHH OUI que c'est beau !
            $token = str_shuffle($char);

            //On check que le token n'est pas deja pris, au cas ou on aurait la poisse
            $repo = $this->manager->getRepository(User::class);
            $userSameToken = $repo->findByToken($token);
            if (empty($userSameToken)) {
                $user->setToken(substr($token, 0, 8));
                $this->manager->flush();
            } else {
                //Oh oui #2 J'ose ou j'ose pas ! Dieu (ou Albe) seul sait si cette instruction marche !
                return $this->getToken();
            }
        }

        return $user->getToken();
    }
}
