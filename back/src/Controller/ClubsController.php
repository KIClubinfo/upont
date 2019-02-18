<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubUser;
use App\Entity\User;
use App\Form\ClubType;
use App\Form\ClubUserType;
use App\Helper\PaginateHelper;
use App\Repository\ClubRepository;
use App\Repository\ClubUserRepository;
use App\Repository\EventRepository;
use App\Repository\NewsitemRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClubsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Club::class, ClubType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les clubs",
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
     * @Route("/clubs", methods={"GET"})
     */
    public function getClubsAction(ClubRepository $clubRepository)
    {
        return $this->json($clubRepository->findAll());
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne un club",
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
     * @Route("/clubs/{slug}", methods={"GET"})
     */
    public function getClubAction($slug)
    {
        $club = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($club);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un club",
     *     @SWG\Parameter(
     *         name="fullName",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="icon",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="presentation",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="active",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="category",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="administration",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="banner",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="place",
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
     * @Route("/clubs", methods={"POST"})
     */
    public function postClubAction()
    {
        $data = $this->post();

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Modifie un club",
     *     @SWG\Parameter(
     *         name="fullName",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="icon",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="presentation",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="active",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="category",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="administration",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="banner",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="place",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
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
     * @Route("/clubs/{slug}", methods={"PATCH"})
     */
    public function patchClubAction($slug)
    {
        $data = $this->patch(
            $slug,
            $this->isClubMember($slug)
            && (!$this->get('security.authorization_checker')->isGranted('ROLE_EXTERIEUR')
                || $slug == $this->user->getSlug()
            )
        );

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un club",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
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
     * @Route("/clubs/{slug}", methods={"DELETE"})
     */
    public function deleteClubAction($slug)
    {
        $repoLink = $this->manager->getRepository(ClubUser::class);
        $club = $this->findBySlug($slug);
        $link = $repoLink->findBy(['club' => $club]);

        foreach ($link as $clubUser) {
            $this->manager->remove($clubUser);
        }

        $this->delete($slug);

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les membres d'un club",
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
     * @Route("/clubs/{slug}/users", methods={"GET"})
     */
    public function getClubUsersAction(ClubUserRepository $clubUserRepository, Club $club)
    {
        $members = $clubUserRepository->findBy(['club' => $club]);

        return $this->json($members);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Ajoute un membre à un club",
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
     * @Route("/clubs/{slug}/users/{username}", methods={"POST"})
     */
    public function postClubUserAction(Request $request, Club $club, User $user)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($club->getSlug()));

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->manager->getRepository(ClubUser::class);
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // On crée la relation si elle n'existe pas déjà
        if (count($link) == 0) {
            // Création de l'entité relation
            $link = new ClubUser();
            $link->setClub($club);
            $link->setUser($user);
            $link->setPriority($user->getId());

            // Validation des données annexes
            $form = $this->createForm(ClubUserType::class, $link, ['method' => 'POST']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->persist($link);
                $this->manager->flush();

                return $this->json(null, 204);
            } else {
                $this->manager->detach($link);
                return $this->json($form, 400);
            }
        } else
            throw new BadRequestHttpException('La relation entre Club et User existe déjà');
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Modifie un membre d'un club",
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
     * @Route("/clubs/{slug}/users/{username}", methods={"PATCH"})
     */
    public function patchClubUserAction(Request $request, Club $club, User $user)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($club->getSlug()));

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->manager->getRepository(ClubUser::class);
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // On édite la relation si elle existe (de façon unique)
        if (count($link) == 1) {
            $link = $link[0];
            // Validation des données annexes
            $form = $this->createForm(ClubUserType::class, $link, ['method' => 'PATCH']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->persist($link);
                $this->manager->flush();

                return $this->json(null, 204);
            } else {
                $this->manager->detach($link);
                return $this->json($form, 400);
            }
        } else
            throw new BadRequestHttpException('Cette personne ne fait pas partie du club !');
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Enlève un membre d'un club",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
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
     * @Route("/clubs/{slug}/users/{username}", methods={"DELETE"})
     */
    public function deleteClubUserAction(Club $club, User $user)
    {
        if (!($this->is('ADMIN') || $this->isClubMember($club->getSlug())))
            throw new AccessDeniedException('Accès refusé');

        // On récupère la relation
        $repoLink = $this->manager->getRepository(ClubUser::class);
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // Supprime la relation si elle existe
        if (count($link) == 1) {
            $this->manager->remove($link[0]);
            $this->manager->flush();
        } else
            throw new NotFoundHttpException('Relation entre Club et User non trouvée');
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Echange les priorité des 2 clubUsers associés aux Users",
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
     * @Route("/clubs/{slug}/users/{username}/{direction}", methods={"PATCH"})
     */
    public function swapPriorityClubUserAction($slug, $username, $direction)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($slug));

        // On récupère les entités concernées
        $repo = $this->manager->getRepository(User::class);
        $user = $repo->findOneByUsername($username);
        $club = $this->findBySlug($slug);

        // Trouve les clubUsers assiciés aux Users
        $clubUserRepository = $this->manager->getRepository(ClubUser::class);
        $clubUser = $clubUserRepository->findOneBy(['club' => $club, 'user' => $user]);

        $priority = $clubUser->getPriority();
        $promo = $user->getPromo();

        if ($direction == 'down') {
            $swappedWith = $clubUserRepository->getUserBelowInClubWithPromo($club, $promo, $priority);
        } else if ($direction == 'up') {
            $swappedWith = $clubUserRepository->getUserAboveInClubWithPromo($club, $promo, $priority);
        } else
            throw new BadRequestHttpException('Direction invalide');

        $clubUser->setPriority($swappedWith->getPriority());
        $swappedWith->setPriority($priority);
        $this->manager->flush();

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Abonne un utilisateur à un club",
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
     * @Route("/clubs/{slug}/follow", methods={"POST"})
     */
    public function followClubAction($slug)
    {
        $user = $this->user;
        $club = $this->findBySlug($slug);

        if (!$user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous êtes déjà abonné à ce club');
        } else {
            $user->removeClubNotFollowed($club);
            $this->manager->flush();

            return $this->json(null, 204);
        }
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Désabonne un utilisateur d'un club",
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
     * @Route("/clubs/{slug}/unfollow", methods={"POST"})
     */
    public function unFollowClubAction(Club $club)
    {
        $user = $this->user;

        if ($user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous n\'êtes pas abonné à ce club');
        } else {
            $user->addClubNotFollowed($club);
            $this->manager->flush();

            return $this->json(null, 204);
        }
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne toutes les news publiées par un club",
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
     * @Route("/clubs/{slug}/newsitems", methods={"GET"})
     */
    public function getNewsitemsClubAction(Club $club, NewsitemRepository $newsitemRepository, PaginateHelper $paginateHelper)
    {
        $paginateHelper = $this->get('App\Helper\PaginateHelper');

        $resultData = $paginateHelper->paginate($newsitemRepository, [
            'authorClub' => $club
        ]);

        return $this->json($resultData, 200);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne tous les events publiés par un club",
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
     * @Route("/clubs/{slug}/events", methods={"GET"})
     */
    public function getEventsClubAction(Club $club, EventRepository $eventRepository, PaginateHelper $paginateHelper)
    {
        $resultData = $paginateHelper->paginate($eventRepository, [
            'authorClub' => $club
        ]);

        return $this->json($resultData, 200);
    }
}
