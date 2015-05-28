<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupsController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Group', 'Users');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les groupes",
     *  output="KI\UpontBundle\Entity\Users\Group",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getGroupsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un groupe",
     *  output="KI\UpontBundle\Entity\Users\Group",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getGroupAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Modifie un groupe",
     *  input="KI\UpontBundle\Form\Users\GroupType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function patchGroupAction($slug) { return $this->patch($slug); }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur à un groupe",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Post("/groups/{slug}/users/{id}")
     */
    public function postUserGroupAction($slug, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $group = $this->findBySlug($slug);
        $user = $this->em->getRepository('KIUpontBundle:Users\User')->findOneByUsername($id);

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if ($user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur appartient déjà à ce groupe');
        } else {
            $user->addGroupUser($group);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retire un utilisateur d'un groupe",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Delete("/groups/{slug}/users/{id}")
     */
    public function removeUserGroupAction($slug, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        $group = $this->findBySlug($slug);
        $user = $this->em->getRepository('KIUpontBundle:Users\User')->findOneByUsername($id);

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if (!$user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur n\'appartient pas à ce groupe');
        } else {
            $user->removeGroupUser($group);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne les utilisateurs appartenant au groupe",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/groups/{slug}/users")
     */
    public function getUsersGroupAction($slug) { return $this->findBySlug($slug)->getUsers(); }

}
