<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use KI\UserBundle\Entity\Notification;
use KI\UserBundle\Entity\Club;

// Fonctions génériques étendant le FOSUserBundle
class CoreController extends FOSRestController
{
    protected $securityContext;
    protected $user = null;

    /**
     * Initialise le controleur
     */
    public function setUser()
    {
        $this->securityContext = $this->get('security.context');
        $token = $this->securityContext->getToken();
        $this->user = $token ? $token->getUser() : null;
    }

    /**
     * Permet de savoir si un utilisateur a un rôle ou non
     * @param  string $role
     * @return boolean
     */
    protected function is($role)
    {
        return $this->securityContext->isGranted('ROLE_'.$role);
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
     * Sert à checker si l'utilisateur actuel est membre du club au nom duquel il poste
     * @param  Club $club
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
        $request = $this->getRequest()->request;
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
