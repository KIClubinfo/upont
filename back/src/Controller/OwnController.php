<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\AchievementUser;
use App\Entity\Club;
use App\Entity\CourseUser;
use App\Entity\Device;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\UserType;
use App\Helper\PaginateHelper;
use App\Repository\FixRepository;
use App\Repository\NewsitemRepository;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Carbon\Carbon;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OwnController extends ResourceController
{

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie des détails sur les achievements et le niveau de l'utilisateur",
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
     * @Route("/own/achievements", methods={"GET"})
     */
    public function getAchievementsAction(Request $request)
    {
        return $this->retrieveAchievements($request, $this->user);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie des détails sur les achievements et le niveau de l'utilisateur",
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
     * @Route("/users/{slug}/achievements", methods={"GET"})
     */
    public function getUserAchievementsAction(Request $request, $slug)
    {
        $user = $this->findBySlug($slug);
        return $this->retrieveAchievements($request, $user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function retrieveAchievements($request, $user)
    {
        $achievementRepository = $this->manager->getRepository(Achievement::class);
        $achievementUserRepository = $this->manager->getRepository(AchievementUser::class);
        $unlocked = [];
        $oUnlocked = [];
        $all = $request->query->has('all');

        $response = $achievementUserRepository->findByUser($user);
        foreach ($response as $achievementUser) {
            $achievement = $achievementUser->getAchievement();
            $oUnlocked[] = $achievement;

            if ($all || !$achievementUser->getSeen()) {
                $unlocked[] = [
                    'id' => $achievement->getIdA(),
                    'name' => $achievement->name(),
                    'description' => $achievement->description(),
                    'points' => $achievement->points(),
                    'image' => $achievement->image(),
                    'date' => $achievementUser->getDate(),
                    'seen' => $achievementUser->getSeen(),
                    'ownedBy' => $achievementUserRepository->getOwnedByCount($achievement),
                ];
                if (!$achievementUser->getSeen())
                    $achievementUser->setSeen(true);
            }
        }
        $all = $achievementRepository->findAll();
        $locked = [];
        $points = 0;
        $factor = 1;

        // On regarde quels achievements sont locked et on en profite pour
        // calculer le nombre de points de l'utilisateur obtenus par les
        // achievements
        foreach ($all as $achievement) {
            if (!in_array($achievement, $oUnlocked)) {
                $locked[] = [
                    'id' => $achievement->getIdA(),
                    'name' => $achievement->name(),
                    'description' => $achievement->description(),
                    'points' => $achievement->points(),
                    'image' => $achievement->image(),
                    'ownedBy' => $achievementUserRepository->getOwnedByCount($achievement),
                ];
            } else {
                if (gettype($achievement->points()) == 'integer') {
                    $points += $achievement->points();
                } else if ($achievement->points() == '+10%') {
                    $factor += 0.1;
                } else if ($achievement->points() == '+15%') {
                    $factor += 0.15;
                } else if ($achievement->points() == '+75%') {
                    $factor += 0.75;
                }
            }
        }

        // On trie les achievements par leur ID
        $ids = [];
        foreach ($unlocked as $key => $achievement) {
            $ids[$key] = $achievement['id'];
        }
        array_multisort($ids, SORT_ASC, $unlocked);
        $ids = [];
        foreach ($locked as $key => $achievement) {
            $ids[$key] = $achievement['id'];
        }
        array_multisort($ids, SORT_ASC, $locked);

        // On renvoie pas mal de données utiles
        $response = Achievement::getLevel($factor * $points);
        $return = [
            'number' => $response['number'],
            'points' => ceil($factor * $points),
            'current_level' => $response['current'],
            'next_level' => isset($response['next']) ? $response['next'] : null,
            'unlocked' => $unlocked,
            'locked' => $locked,
        ];

        $this->manager->flush();
        return $this->json($return);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne la liste des smartphones enregistrés",
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
     * @Route("/own/devices", methods={"GET"})
     */
    public function getDevicesAction()
    {
        if (!$this->is('USER'))
            throw new AccessDeniedException();

        $user = $this->user;
        return $this->json($user->getDevices());
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Enregistre un smartphone auprès de l'API",
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
     *     )
     * )
     *
     * @Route("/own/devices", methods={"POST"})
     */
    public function postDeviceAction(Request $request)
    {
        if (!$this->is('USER')) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('device'))
            throw new BadRequestHttpException('Identifiant de téléphone manquant');
        if (!$request->request->has('type'))
            throw new BadRequestHttpException('Type de téléphone manquant');

        // On vérifie que le smartphone n'a pas déjà été enregistré
        $repo = $this->manager->getRepository(Device::class);
        $devices = $repo->findByDevice($request->request->get('device'));
        if (!empty($devices))
            return $this->json(null, 204);

        $device = new Device();
        $device->setOwner($this->user);
        $device->setDevice($request->request->get('device'));
        $device->setType($request->request->get('type'));
        $this->manager->persist($device);
        $this->manager->flush();

        return $this->json($device, 201);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un smartphone enregistré",
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
     *         description="Aucun résultat ne correspond au token transmis"
     *     )
     * )
     *
     * @Route("/own/devices/{id}", methods={"DELETE"})
     */
    public function deleteDeviceAction($id)
    {
        if (!$this->is('USER')) {
            throw new AccessDeniedException();
        }

        $repo = $this->manager->getRepository(Device::class);
        $device = $repo->findOneByDevice(str_replace('"', '', $id));

        if ($device === null)
            throw new NotFoundHttpException('Téléphone non trouvé');

        $this->manager->remove($device);
        $this->manager->flush();

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie les notifications non lues de l'utilisateur actuel",
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
     * @Route("/own/notifications", methods={"GET"})
     */
    public function getNotificationsAction()
    {
        $repo = $this->manager->getRepository(Notification::class);
        $user = $this->user;

        // On récupère toutes les notifs
        $notifications = $repo->findAll();
        $return = [];

        // On filtre celles qui sont uniquement destinées à l'utilisateur actuel
        foreach ($notifications as $notification) {
            $mode = $notification->getMode();
            if ($mode == 'to') {
                // Si la notification n'a pas été lue
                if ($notification->getRecipients()->contains($user) && !$notification->getReads()->contains($user))
                    $return[] = $notification;
            } else if ($mode == 'exclude') {
                // Si la notification n'a pas été lue
                if (!$notification->getReads()->contains($user) && !$notification->getRecipients()->contains($user))
                    $return[] = $notification;
            } else
                throw new \Exception('Notification : mode d\'envoi inconnu (' . $mode . ')');
        }

        // On marque chaque notification récupérée comme lue
        foreach ($return as $notification) {
            $notification->addRead($user);
        }
        $this->manager->flush();

        return $this->json($return);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des clubs suivis",
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
     * @Route("/own/followed", methods={"GET"})
     */
    public function getFollowedAction()
    {
        return $this->json($this->getFollowedClubs());
    }

    protected function getFollowedClubs($user = null)
    {
        $repo = $this->manager->getRepository(Club::class);
        if ($user === null)
            $user = $this->user;
        $userNotFollowed = $user->getClubsNotFollowed();

        $clubs = $repo->findAll();
        $return = [];
        foreach ($clubs as $club) {
            if (!$userNotFollowed->contains($club)) {
                $return[] = $club;
            }
        }
        return $return;
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des évènements suivis et persos",
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
     * @Route("/own/events", methods={"GET"})
     */
    public function getOwnEventsAction(Request $request, UserRepository $userRepository)
    {
        $limit = $request->query->get('limit', 100);
        $page = $request->query->get('page', 1);

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $count = $userRepository->countFollowedEvents($user);

        $events = $userRepository->findFollowedEvents($user, $limit, $page);

        $totalPages = (int)ceil($count / $limit);

        return $this->json([
            'data' => $events,
            'pagination_params' => [
                'sort' => '',
                'limit' => $limit,
                'page' => $page,
            ],
            'pagination_infos' => [
                'first_page' => 1,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'current_page' => $page,
                'next_page' => $page < $totalPages ? $page + 1 : null,
                'last_page' => $totalPages,
                'total_pages' => $totalPages,

                'count' => $count,
            ]
        ]);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie le calendrier pour la période demandée (liste des cours et évènements suivis)",
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
     * @Route("/own/calendar", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getOwnCalendarAction(Request $request, UserRepository $userRepository)
    {
        $from = $request->query->get('from')
            ? Carbon::parse($request->query->get('from'))
            : null;
        $to = $request->query->get('to')
            ? Carbon::parse($request->query->get('to'))
            : null;

        $events = $userRepository->findFollowedEventsBetween($this->getUser(), $from, $to);

        return $this->json($events);
    }

    private function getCourseItems($user = null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(CourseUser::class);

        if ($user === null)
            $user = $this->user;

        // On extraie les Courseitem et on les trie par date de début
        $result = [];
        $timestamp = [];
        foreach ($repo->findBy(['user' => $user]) as $courseUser) {
            $course = $courseUser->getCourse();
            foreach ($course->getCourseitems() as $courseitem) {
                if ($courseUser->getGroup() == $courseitem->getGroup() || $courseitem->getGroup() == 0) {
                    $result[] = $courseitem;
                    $timestamp[] = $courseitem->getStartDate();
                }
            }
        }
        array_multisort($timestamp, SORT_ASC, $result);

        return $result;
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des news suivies",
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
     * @Route("/own/newsitems", methods={"GET"})
     * @param NewsitemRepository $newsitemRepository
     * @param PaginateHelper $paginateHelper
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getNewsItemsAction(NewsitemRepository $newsitemRepository, PaginateHelper $paginateHelper)
    {
        $resultData = $paginateHelper->paginate($newsitemRepository, [
            'authorClub' => $this->getFollowedClubs()
        ]);

        return $this->json($resultData, 200);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des cours suivis",
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
     * @Route("/own/courses", methods={"GET"})
     */
    public function getOwnCoursesAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(CourseUser::class);

        $return = [];
        foreach ($repo->findBy(['user' => $this->user]) as $courseUser) {
            $return[] = ['course' => $courseUser->getCourse(), 'group' => $courseUser->getGroup()];
        }

        return $this->json($return);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des prochains cours de l'utilisateur",
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
     * @Route("/own/courseitems", methods={"GET"})
     */
    public function getCourseitemsAction()
    {
        return $this->json($this->getCourseItems());
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Change ou ajoute une préférence",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d'information à renvoyer"
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
     * @Route("/own/preferences", methods={"PATCH"})
     */
    public function changePreferenceAction(Request $request)
    {
        $user = $this->user;

        if (!$this->is('USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!($request->request->has('key') && $request->request->has('value')))
            throw new BadRequestHttpException('Champ manquant');

        if ($user->addPreference($request->request->get('key'), $request->request->get('value'))) {
            $this->manager->flush();
            return $this->json(null, 204);
        }

        throw new BadRequestHttpException('Cette préférence n\'existe pas');
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un préférence",
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
     * @Route("/own/preferences", methods={"DELETE"})
     */
    public function removePreferenceAction(Request $request)
    {
        $user = $this->user;

        if (!$this->is('USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!($request->request->has('key')))
            throw new BadRequestHttpException('Champ manquant');

        if ($user->removePreference($request->request->get('key'))) {
            $this->manager->flush();

            return $this->json(null, 204);
        }

        throw new BadRequestHttpException('Cette préférence n\'existe pas');
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie les préférences de l'utilisateur courant",
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
     * @Route("/own/preferences", methods={"GET"})
     */
    public function getPreferencesAction()
    {
        $user = $this->user;
        return $this->json($user->getPreferences());
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un token si non existant et le retourne",
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
     * @Route("/own/token", methods={"GET"})
     */
    public function getTokenAction(TokenService $tokenService)
    {
        return $this->json([
            'token' => $tokenService->getToken()
        ]);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie la liste des dépannages demandés par l'utilisateur",
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
     * @Route("/own/fixs", methods={"GET"})
     */
    public function getOwnFixsAction(FixRepository $fixRepository, PaginateHelper $paginateHelper)
    {
        if (!$this->is('USER'))
            throw new AccessDeniedException('Accès refusé');

        $resultData = $paginateHelper->paginate($fixRepository, [
            'user' => $this->user
        ]);

        return $this->json($resultData, 200);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie l'utilisateur actuel",
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
     * @Route("/own/user", methods={"GET"})
     */
    public function getOwnUserAction()
    {
        return $this->json($this->user);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie les clubs de l'utilisateur actuel",
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
     * @Route("/own/clubs", methods={"GET"})
     */
    public function getOwnClubsAction()
    {
        return $this->json($this->user->getClubs());
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Met à jour les informations du compte",
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
     * @Route("/own/user", methods={"POST"})
     */
    public function postOwnUserAction(Request $request)
    {
        if (!$request->request->has('password') || !$request->request->has('confirm') || !$request->request->has('old'))
            throw new BadRequestHttpException('Champs password/confirm non rempli(s)');

        if ($this->user->hasRole('ROLE_ADMISSIBLE'))
            return $this->json(null, 403);

        // Pour changer le mot de passe on doit passer par le UserManager
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($this->user->getUsername());

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($request->request->get('old'), $user->getSalt());

        if ($encodedPassword != $user->getPassword())
            throw new BadRequestHttpException('Ancien mot de passe incorrect');

        $user->setPlainPassword($request->request->get('password'));
        $userManager->updateUser($user, true);

        return $this->json(null, 204);
    }
}
