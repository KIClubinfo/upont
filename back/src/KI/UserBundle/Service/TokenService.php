<?php

namespace KI\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use KI\UserBundle\Entity\User;

class TokenService
{
    protected $manager;
    protected $securityContext;

    public function __construct(EntityManager $manager, SecurityContext $securityContext)
    {
        $this->manager         = $manager;
        $this->securityContext = $securityContext;
    }

    // Génère un token pour l'utilisateur
    // On n'utilise pas le JWT pour avoir un token plus personnalisable et moins long
    // (donc moins moche)
    public function getToken(User $user = null)
    {
        if ($user === null) {
            if ($result = $this->securityContext->getToken())
                $user = $result->getUser();
            else
                return;
        }


        if (empty($user->getToken())) {
            $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            //OHHHH OUI que c'est beau !
            $token = str_shuffle($char);

            //On check que le token n'est pas deja pris, au cas ou on aurait la poisse
            $repo = $this->manager->getRepository('KIUserBundle:User');
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
