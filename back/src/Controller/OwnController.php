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
use App\Service\CalendarService;
use App\Service\TokenService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OwnController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie des détails sur les achievements et le niveau de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/achievements")
     * @Method("GET")
     */
    public function getAchievementsAction(Request $request)
    {
        return $this->retrieveAchievements($request, $this->user);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie des détails sur les achievements et le niveau de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{slug}/achievements")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Retourne la liste des smartphones enregistrés",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/devices")
     * @Method("GET")
     */
    public function getDevicesAction()
    {
        if (!$this->is('USER'))
            throw new AccessDeniedException();

        $user = $this->user;
        return $this->json($user->getDevices());
    }

    /**
     * @ApiDoc(
     *  description="Enregistre un smartphone auprès de l'API",
     *  requirements={
     *   {
     *    "name"="device",
     *    "dataType"="string",
     *    "description"="Identifiant du téléphone"
     *   },
     *   {
     *    "name"="type",
     *    "dataType"="string",
     *    "description"="Android | iOS | WP"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/devices")
     * @Method("POST")
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

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un smartphone enregistré",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Aucun résultat ne correspond au token transmis",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/devices/{id}")
     * @Method("DELETE")
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
     * @ApiDoc(
     *  description="Renvoie les notifications non lues de l'utilisateur actuel",
     *  output="App\Entity\Notification",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/notifications")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Renvoie la liste des clubs suivis",
     *  output="App\Entity\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/followed")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Renvoie la liste des évènements suivis et persos",
     *  output="App\Entity\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/events")
     * @Method("GET")
     */
    public function getOwnEventsAction(Request $request, UserRepository $userRepository)
    {
        $limit = $request->query->get('limit');
        $page = $request->query->get('page');

        $events = $userRepository->findFollowedEvents(
            $this->getUser()->getId(),
            $limit,
            $page
        );

        return $this->json([
            'data' => $events,
            'pagination_params' => [
                'find_by' => [],
                'sort_by' => '',

                'limit' => $limit,
                'page' => $page,
            ],
            'pagination_infos' => [
                'first_page' => 1,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'current_page' => $page,
                'next_page' => $page + 1,
            ]
        ]);
    }

    /**
     * @ApiDoc(
     *  description="Retourne le calendrier de l'utilisateur au format ICS",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{token}/calendar")
     * @Method("GET")
     */
    public function getOwnCalendarAction(CalendarService $calendarService, $token)
    {
        $user = $this->repository->findOneByToken($token);
        if ($user === null) {
            throw new NotFoundHttpException('Aucun utilisateur ne correspond au token saisi');
        } else {
            $userRepository = $this->manager->getRepository(User::class);

            $events = $userRepository->findFollowedEvents($user->getId());
            $courses = $this->getCourseitems($user);

            $calStr = $calendarService->getCalendar($user, $events, $courses);

            return new Response($calStr, 200, [
                    'Content-Type' => 'text/calendar; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="calendar.ics"',
                ]
            );
        }
    }

    private function getCourseitems($user = null)
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
     * @ApiDoc(
     *  description="Renvoie la liste des news suivies",
     *  output="App\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/newsitems")
     * @Method("GET")
     */
    public function getNewsItemsAction(NewsitemRepository $newsitemRepository, PaginateHelper $paginateHelper)
    {
        $resultData = $paginateHelper->paginate($newsitemRepository, [
            'authorClub' => $this->getFollowedClubs()
        ]);

        return $this->json($resultData, 200);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des cours suivis",
     *  output="App\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/courses")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Renvoie la liste des prochains cours de l'utilisateur",
     *  output="App\Entity\Courseitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/courseitems")
     * @Method("GET")
     */
    public function getCourseitemsAction()
    {
        return $this->json($this->getCourseitems());
    }

    /**
     * @ApiDoc(
     *  description="Change ou ajoute une préférence",
     *  requirements={
     *   {
     *    "name"="key",
     *    "dataType"="string",
     *    "description"="Préférence éditée ou changée"
     *   },
     *   {
     *    "name"="value",
     *    "dataType"="string",
     *    "description"="Valeur de la préférence"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d'information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/preferences")
     * @Method("PATCH")
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
     * @ApiDoc(
     *  description="Supprime un préférence",
     *  requirements={
     *   {
     *    "name"="key",
     *    "dataType"="string",
     *    "description"="Préférence supprimée"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/preferences")
     * @Method("DELETE")
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
     * @ApiDoc(
     *  description="Renvoie les préférences de l'utilisateur courant",
     *  output="array",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/preferences")
     * @Method("GET")
     */
    public function getPreferencesAction()
    {
        $user = $this->user;
        return $this->json($user->getPreferences());
    }

    /**
     * @ApiDoc(
     *  description="Crée un token si non existant et le retourne",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/token")
     * @Method("GET")
     */
    public function getTokenAction(TokenService $tokenService)
    {
        return $this->json([
            'token' => $tokenService->getToken()
        ]);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des dépannages demandés par l'utilisateur",
     *  output="App\Entity\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/fixs")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Renvoie l'utilisateur actuel",
     *  output="App\Entity\User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/user")
     * @Method("GET")
     */
    public function getOwnUserAction()
    {
        return $this->json($this->user);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie les clubs de l'utilisateur actuel",
     *  output="App\Entity\ClubUser",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/clubs")
     * @Method("GET")
     */
    public function getOwnClubsAction()
    {
        return $this->json($this->user->getClubs());
    }

    /**
     * @ApiDoc(
     *  description="Met à jour les informations du compte",
     *  requirements={
     *   {
     *    "name"="old",
     *    "dataType"="string",
     *    "description"="L'ancien mot de passe"
     *   },
     *   {
     *    "name"="password",
     *    "dataType"="string",
     *    "description"="Le nouveau mot de passe"
     *   },
     *   {
     *    "name"="confirm",
     *    "dataType"="string",
     *    "description"="Le mot de passe une seconde fois (confirmation)"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/user")
     * @Method("POST")
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
