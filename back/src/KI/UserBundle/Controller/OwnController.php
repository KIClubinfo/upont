<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\Device;
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
        $this->initialize('User', 'User');
    }

    /**
     * @ApiDoc(
     *  description="Renvoie des détails sur les achievements et le niveau de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
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
        $achievementRepository = $this->manager->getRepository('KIUserBundle:Achievement');
        $achievementUserRepository = $this->manager->getRepository('KIUserBundle:AchievementUser');
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
                    'ownedBy' => $achievementUserRepository->createQueryBuilder('au')
                        ->select('count(au)')
                        ->where('au.achievement = :achievement')
                        ->setParameter('achievement', $achievement)
                        ->getQuery()
                        ->getSingleScalarResult(),
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
                    'ownedBy' => $achievementUserRepository->createQueryBuilder('au')
                        ->select('count(au)')
                        ->where('au.achievement = :achievement')
                        ->setParameter('achievement', $achievement)
                        ->getQuery()
                        ->getSingleScalarResult(),
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/devices")
     * @Method("GET")
     */
    public function getDevicesAction()
    {
        if (!$this-is('USER'))
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
     *   503="Service temporairement indisponible ou en maintenance",
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
        $repo = $this->manager->getRepository('KIUserBundle:Device');
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
     *   503="Service temporairement indisponible ou en maintenance",
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

        $repo = $this->manager->getRepository('KIUserBundle:Device');
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
     *  output="KI\UserBundle\Entity\Notification",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/notifications")
     * @Method("GET")
     */
    public function getNotificationsAction()
    {
        $repo = $this->manager->getRepository('KIUserBundle:Notification');
        $user = $this->user;

        // On récupère toutes les notifs
        $notifications = $repo->findAll();
        $return = [];

        // On filtre celles qui sont uniquement destinées à l'utilisateur actuel
        foreach ($notifications as $notification) {
            $mode = $notification->getMode();
            if ($mode == 'to') {
                // Si la notification n'a pas été lue
                if ($notification->getRecipient()->contains($user) && !$notification->getRead()->contains($user))
                    $return[] = $notification;
            } else if ($mode == 'exclude') {
                // Si la notification n'a pas été lue
                if (!$notification->getRead()->contains($user) && !$notification->getRecipient()->contains($user))
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
     *  output="KI\UserBundle\Entity\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
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
        $repo = $this->manager->getRepository('KIUserBundle:Club');
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
     *  output="KI\PublicationBundle\Entity\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/events")
     * @Method("GET")
     */
    public function getOwnEventsAction(Request $request)
    {
        // Si on prend tout on renvoie comme ça
        if ($request->query->has('all'))
            return $this->json($this->getFollowedEvents());

        $events = $this->manager->getRepository('KIUserBundle:User')->findAllFollowedEvents($this->user->getId());
        return $this->json($events);
    }

    /**
     * @ApiDoc(
     *  description="Retourne le calendrier de l'utilisateur au format ICS",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{token}/calendar")
     * @Method("GET")
     */
    public function getOwnCalendarAction($token)
    {
        $user = $this->repository->findOneByToken($token);
        if ($user === null) {
            throw new NotFoundHttpException('Aucun utilisateur ne correspond au token saisi');
        } else {
            $events = $this->getFollowedEvents($user);
            $courses = $this->getCourseitems($user);

            $calStr = $this->get('ki_publication.service.calendar')->getCalendar($user, $events, $courses);

            return new Response($calStr, 200, [
                    'Content-Type' => 'text/calendar; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="calendar.ics"',
                ]
            );
        }
    }

    // Va chercher les événements suivis
    private function getFollowedEvents($user = null)
    {
        $repo = $this->manager->getRepository('KIPublicationBundle:Event');

        if ($user === null)
            $user = $this->user;

        $followedEvents = $repo->findBy(['authorClub' => $this->getFollowedClubs($user)]);
        $persoEvents = $repo->findBy(['authorUser' => $user, 'authorClub' => null]);
        $events = array_merge($followedEvents, $persoEvents);

        // Tri et élimination des données
        $dates = [];
        $return = [];
        foreach ($events as $key => $event) {
            // On enlève l'événement si l'élève l'a masqué
            if ($event->getPookies()->contains($user))
                continue;

            // On trie par date
            $return[$key] = $event;
            $dates[$key] = $event->getStartDate();
        }
        array_multisort($dates, SORT_DESC, $return);

        return $return;
    }

    private function getCourseitems($user = null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIPublicationBundle:CourseUser');

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
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/newsitems")
     * @Method("GET")
     */
    public function getNewsItemsAction()
    {
        $repository = $this->manager->getRepository('KIPublicationBundle:Newsitem');

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));
        $findBy['authorClub'] = $this->getFollowedClubs();
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);

        // Tri des données
        $dates = [];
        foreach ($results as $key => $newsitem) {
            $results[$key] = $newsitem;
            $dates[$key] = $newsitem->getDate();
        }
        array_multisort($dates, SORT_DESC, $results);

        list($results, $links, $count) = $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);

        return $this->json($results, 200, [
            'Links' => implode(',', $links),
            'Total-count' => $count
        ]);

    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des cours suivis",
     *  output="KI\PublicationBundle\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/courses")
     * @Method("GET")
     */
    public function getOwnCoursesAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIPublicationBundle:CourseUser');

        $return = [];
        foreach ($repo->findBy(['user' => $this->user]) as $courseUser) {
            $return[] = ['course' => $courseUser->getCourse(), 'group' => $courseUser->getGroup()];
        }

        return $this->json($return);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des prochains cours de l'utilisateur",
     *  output="KI\PublicationBundle\Entity\Courseitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/token")
     * @Method("GET")
     */
    public function getTokenAction()
    {
        return $this->json([
            'token' => $this->get('ki_user.service.token')->getToken()
        ]);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des dépannages demandés par l'utilisateur",
     *  output="KI\ClubinfoBundle\Entity\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/own/fixs")
     * @Method("GET")
     */
    public function getOwnFixsAction()
    {
        $user = $this->user;
        if (!$this->is('USER'))
            throw new AccessDeniedException('Accès refusé');

        $repository = $this->manager->getRepository('KIClubinfoBundle:Fix');
        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));

        $findBy['user'] = $user;
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);

        list($results, $links, $count) = $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);

        return $this->json($results, 200, [
            'Links' => implode(',', $links),
            'Total-count' => $count
        ]);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie l'utilisateur actuel",
     *  output="KI\UserBundle\Entity\User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
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
     *   503="Service temporairement indisponible ou en maintenance",
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
