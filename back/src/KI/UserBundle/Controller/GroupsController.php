<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Group', 'User');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les groupes",
     *  output="KI\UserBundle\Entity\Group",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/groups")
     * @Method("GET")
     */
    public function getGroupsAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un groupe",
     *  output="KI\UserBundle\Entity\Group",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/groups/{slug}")
     * @Method("GET")
     */
    public function getGroupAction($slug)
    {
        $group = $this->getOne($slug);

        return $this->json($group);
    }

    /**
     * @ApiDoc(
     *  description="Crée un groupe",
     *  input="KI\UserBundle\Form\GroupType",
     *  output="KI\UserBundle\Entity\Group",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/groups")
     * @Method("POST")
     */
    public function postGroupAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if (!$request->request->has('name') || !$request->request->has('role'))
            throw new BadRequestHttpException('Les champs "name" et "role" sont obligatoires');

        $group = new $this->class($request->request->get('name'));

        $role = $request->request->get('role');
        if (!is_string($role))
            throw new UnexpectedTypeException($role, 'string');

        $group->setRoles([$role]);

        $this->manager->persist($group);
        $this->manager->flush();
        return $this->json($group, 201);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un groupe",
     *  input="KI\UserBundle\Form\GroupType",
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
     * @Route("/groups/{slug}")
     * @Method("PATCH")
     */
    public function patchGroupAction(Request $request, $slug)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if ($slug === null)
            throw new BadRequestHttpException('Le groupe n\'existe pas');

        $group = $this->findBySlug($slug);

        if ($request->request->has('name')) {
            $name = $request->request->get('name');
            if (!is_string($name))
                throw new UnexpectedTypeException($name, 'string');

            $group->setName([$name]);
            $request->request->remove('name');
        }

        if ($request->request->has('role')) {
            $role = $request->request->get('role');
            if (!is_string($role))
                throw new UnexpectedTypeException($role, 'string');

            $group->setRoles([$role]);
            $request->request->remove('role');
        }

        if (count($request->request->all()) > 0)
            throw new BadRequestHttpException('Ce champ n\'existe pas');

        $this->manager->persist($group);
        $this->manager->flush();
        return $this->json($group, 204);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un groupe",
     *  input="KI\UserBundle\Form\GroupType",
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
     * @Route("/groups/{slug}")
     * @Method("DELETE")
     */
    public function deleteGroupAction($slug)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if ($slug === null)
            throw new BadRequestHttpException('Le groupe n\'existe pas');

        $this->manager->remove($this->findBySlug($slug));
        $this->manager->flush();

        return $this->json(null, 204);
    }

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
     * @Route("/groups/{slug}/users/{id}")
     * @Method("POST")
     */
    public function postUserGroupAction($slug, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $group = $this->findBySlug($slug);
        $user = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($id);

        if (!$user instanceof User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if ($user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur appartient déjà à ce groupe');
        } else {
            $user->addGroupUser($group);
            $this->manager->flush();

            return $this->json(null, 204);
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
     * @Route("/groups/{slug}/users/{id}")
     * @Method("DELETE")
     */
    public function removeUserGroupAction($slug, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        $group = $this->findBySlug($slug);
        $user = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($id);

        if (!$user instanceof User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if (!$user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur n\'appartient pas à ce groupe');
        } else {
            $user->removeGroupUser($group);
            $this->manager->flush();

            return $this->json(null, 204);
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
     * @Route("/groups/{slug}/users")
     * @Method("GET")
     */
    public function getUsersGroupAction($slug)
    {
        $groupUsers = $this->findBySlug($slug)->getUsers();

        return $this->json($groupUsers);
    }

}
