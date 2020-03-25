<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GroupsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Group::class, GroupType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les groupes",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/groups", methods={"GET"})
     */
    public function getGroupsAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne un groupe",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}", methods={"GET"})
     */
    public function getGroupAction($slug)
    {
        $group = $this->getOne($slug);

        return $this->json($group);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un groupe",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="role",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/groups", methods={"POST"})
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Modifie un groupe",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="role",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}", methods={"PATCH"})
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
        return $this->json($group, 200);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un groupe",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}", methods={"DELETE"})
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Ajoute un utilisateur à un groupe",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}/users/{id}", methods={"POST"})
     */
    public function postUserGroupAction($slug, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $group = $this->findBySlug($slug);
        $user = $this->manager->getRepository(User::class)->findOneByUsername($id);

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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retire un utilisateur d'un groupe",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}/users/{id}", methods={"DELETE"})
     */
    public function removeUserGroupAction($slug, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        $group = $this->findBySlug($slug);
        $user = $this->manager->getRepository(User::class)->findOneByUsername($id);

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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne les utilisateurs appartenant au groupe",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/groups/{slug}/users", methods={"GET"})
     */
    public function getUsersGroupAction($slug)
    {
        $groupUsers = $this->findBySlug($slug)->getUsers();

        return $this->json($groupUsers);
    }

}
