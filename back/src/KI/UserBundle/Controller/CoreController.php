<?php

namespace KI\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CoreController extends Controller
{
    protected $user = null;

    /**
     * Initialise le controleur
     */
    public function setUser()
    {
        $token = $this->get('security.token_storage')->getToken();
        $this->user = $token ? $token->getUser() : null;
    }

    /**
     * Permet de savoir si un utilisateur a un rôle ou non
     * @param  string $role
     * @return boolean
     */
    protected function is($role)
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_'.$role);
    }

    /**
     * Éjecte tous les utilisateurs ne respectant pas la condition
     * @param  boolean $bool
     * @return boolean
     */
    protected function trust($bool)
    {
        if ($this->user === null || !$bool) {
            throw new AccessDeniedException('Accès refusé');
        }
    }

    /**
     * Sert à checker si l'user actuel est membre du club au nom duquel il poste
     * @param  string $club
     * @return boolean
     */
    protected function isClubMember($club = null)
    {
        if ($this->is('ADMISSIBLE')) {
            return false;
        }

        // On vérifie que la requete est valide.
        // Si aucun club n'est précisé, c'est qu'on publie à son nom
        // (par exemple message perso) donc ok
        $request = $this->get('request_stack')->getCurrentRequest()->request;
        if (!$request->has('authorClub') && $club === null) {
            return $this->is('USER');
        }

        $repo = $this->manager->getRepository('KIUserBundle:Club');
        $club = $repo->findOneBySlug($request->has('authorClub') ? $request->get('authorClub') : $club);

        if (!$club) {
            return false;
        }

        // On vérifie que l'utilisateur fait bien partie du club
        return $this->get('ki_user.service.permission')->isClubMember($this->user, $club);
    }
}
