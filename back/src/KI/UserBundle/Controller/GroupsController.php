<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupsController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
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
     */
    public function getGroupsAction() { return $this->getAll(); }

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
     */
    public function getGroupAction($slug) { return $this->getOne($slug); }

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
     * @Route\Post("/groups")
     */
    public function postGroupAction(Request $request) {
        if (!$this->get('security.context')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if (!$request->request->has('name') || !$request->request->has('role'))
            throw new BadRequestHttpException('Les champs "name" et "role" sont obligatoires');

        $group = new $this->class($request->request->get('name'));

        $role = $request->request->get('role');
        if (!is_string($role))
            throw new UnexpectedTypeException($role, 'string');

        $group->setRoles(array($role));

        $this->manager->persist($group);
        $this->manager->flush();
        return RestView::create($group,
            201,
            array(
                'Location' => $this->generateUrl(
                    'get_group',
                    array('slug' => $group->getSlug()),
                    true
                )
            )
        );
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
     * @Route\Patch("/groups/{slug}")
     */
    public function patchGroupAction(Request $request, $slug) {
        if (!$this->get('security.context')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if ($slug === null)
            throw new BadRequestHttpException('Le groupe n\'existe pas');

        $group = $this->getOne($slug);

        if ($request->request->has('name')) {
            $name = $request->request->get('name');
            if (!is_string($name))
                throw new UnexpectedTypeException($name, 'string');

            $group->setName(array($name));
            $request->request->remove('name');
        }

        if ($request->request->has('role')) {
            $role = $request->request->get('role');
            if (!is_string($role))
                throw new UnexpectedTypeException($role, 'string');

            $group->setRoles(array($role));
            $request->request->remove('role');
        }

        if (count($request->request->all()) > 0)
            throw new BadRequestHttpException('Ce champ n\'existe pas');

        $this->manager->persist($group);
        $this->manager->flush();
        return RestView::create($group,
            204,
            array(
                'Location' => $this->generateUrl(
                    'get_group',
                    array('slug' => $group->getSlug()),
                    true
                )
            )
        );
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
     * @Route\Delete("/groups/{slug}")
     */
    public function deleteGroupAction($slug) {
        if (!$this->get('security.context')->isGranted('ROLE_MODO'))
            throw new AccessDeniedException();

        if ($slug === null)
            throw new BadRequestHttpException('Le groupe n\'existe pas');

        $this->manager->remove($this->getOne($slug));
        $this->manager->flush();
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
     * @Route\Post("/groups/{slug}/users/{id}")
     */
    public function postUserGroupAction($slug, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $group = $this->findBySlug($slug);
        $user = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($id);

        if (!$user instanceof \KI\UserBundle\Entity\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if ($user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur appartient déjà à ce groupe');
        } else {
            $user->addGroupUser($group);
            $this->manager->flush();

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
        $user = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($id);

        if (!$user instanceof \KI\UserBundle\Entity\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if (!$user->getGroups()->contains($group)) {
            throw new BadRequestHttpException('L\'utilisateur n\'appartient pas à ce groupe');
        } else {
            $user->removeGroupUser($group);
            $this->manager->flush();

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
