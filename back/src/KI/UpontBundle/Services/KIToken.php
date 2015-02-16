<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

class KIToken extends ContainerAware
{
    // Génère un token pour l'utilisateur
    // On n'utilise pas le JWT pour avoir un token plus personnalisable et moins long
    // (donc moins moche)
    public function getToken(\KI\UpontBundle\Entity\Users\User $user = null)
    {
        if ($user === null) {
            if ($result = $this->container->get('security.context')->getToken())
                $user = $result->getUser();
            else
                return;
        }


        if ($user->getToken() == null) {
            $manager = $this->container->get('doctrine.orm.entity_manager');
            $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            //OHHHH OUI que c'est beau !
            $token = str_shuffle($char);

            //On check que le token n'est pas deja pris, au cas ou on aurait la poisse
            $repo = $manager->getRepository('KIUpontBundle:Users\User');
            $userSameToken = $repo->findByToken($token);
            if ($userSameToken == null) {
                $user->setToken(substr($token, 0, 8));
                $manager->flush();
            } else {
                //Oh oui #2 J'ose ou j'ose pas ! Dieu (ou Albe) seul sait si cette instruction marche !
                return $this->getToken();
            }
        }

        return $user->getToken();
    }
}
