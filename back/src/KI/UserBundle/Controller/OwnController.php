<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\Device;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OwnController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
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
     * @Route\Get("/own/achievements")
     */
    public function getAchievementsAction()
    {
        return $this->retrieveAchievements($this->user);
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
     * @Route\Get("/users/{slug}/achievements")
     */
    public function getUserAchievementsAction($slug)
    {
        $user = $this->findBySlug($slug);
        return $this->retrieveAchievements($user);
    }

    private function retrieveAchievements($user)
    {
        $repoA = $this->manager->getRepository('KIUserBundle:Achievement');
        $repoAU = $this->manager->getRepository('KIUserBundle:AchievementUser');
        $unlocked = array();
        $oUnlocked = array();
        $all = $this->getRequest()->query->has('all');

        $response = $repoAU->findByUser($user);
        foreach ($response as $achievementUser) {
            $achievement = $achievementUser->getAchievement();
            $oUnlocked[] = $achievement;

            if ($all || !$achievementUser->getSeen()) {
                $unlocked[] = array(
                    'id'          => $achievement->getIdA(),
                    'name'        => $achievement->name(),
                    'description' => $achievement->description(),
                    'points'      => $achievement->points(),
                    'image'       => $achievement->image(),
                    'date'        => $achievementUser->getDate(),
                    'seen'        => $achievementUser->getSeen(),
                    'ownedBy'     => count($repoAU->findByAchievement($achievement)),
                );
                if (!$achievementUser->getSeen())
                    $achievementUser->setSeen(true);
            }
        }
        $all = $repoA->findAll();
        $locked = array();
        $points = 0;
        $factor = 1;

        // On regarde quels achievements sont locked et on en profite pour
        // calculer le nombre de points de l'utilisateur obtenus par les
        // achievements
        foreach ($all as $achievement) {
            if (!in_array($achievement, $oUnlocked)) {
                $locked[] = array(
                    'id'          => $achievement->getIdA(),
                    'name'        => $achievement->name(),
                    'description' => $achievement->description(),
                    'points'      => $achievement->points(),
                    'image'       => $achievement->image(),
                    'ownedBy'     => count($repoAU->findByAchievement($achievement)),
                );
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
        $ids = array();
        foreach ($unlocked as $key => $achievement) {
            $ids[$key] = $achievement['id'];
        }
        array_multisort($ids, SORT_ASC, $unlocked);
        $ids = array();
        foreach ($locked as $key => $achievement) {
                    $ids[$key] = $achievement['id'];
        }
        array_multisort($ids, SORT_ASC, $locked);

        // On renvoie pas mal de données utiles
        $response = Achievement::getLevel($factor*$points);
        $return = array(
            'number'        => $response['number'],
            'points'        => ceil($factor*$points),
            'current_level' => $response['current'],
            'next_level'    => isset($response['next']) ? $response['next'] : null,
            'unlocked'      => $unlocked,
            'locked'        => $locked,
        );

        $this->manager->flush();
        return $this->jsonResponse($return);
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
     * @Route\Get("/own/devices")
     */
    public function getDevicesAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        $user = $this->get('security.context')->getToken()->getUser();
        return $this->restResponse($user->getDevices());
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
     * @Route\Post("/own/devices")
     */
    public function postDeviceAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest()->request;
        if (!$request->has('device'))
            throw new BadRequestHttpException('Identifiant de téléphone manquant');
        if (!$request->has('type'))
            throw new BadRequestHttpException('Type de téléphone manquant');

        // On vérifie que le smartphone n'a pas déjà été enregistré
        $repo = $this->manager->getRepository('KIUserBundle:Device');
        $devices = $repo->findByDevice($request->get('device'));
        if (!empty($devices))
            return $this->jsonResponse(null, 204);

        $device = new Device();
        $device->setOwner($this->get('security.context')->getToken()->getUser());
        $device->setDevice($request->get('device'));
        $device->setType($request->get('type'));
        $this->manager->persist($device);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
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
     * @Route\Delete("/own/devices/{id}")
     */
    public function deleteDeviceAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $repo = $this->manager->getRepository('KIUserBundle:Device');
        $device = $repo->findOneByDevice(str_replace('"', '', $id));

        if ($device === null)
            throw new NotFoundHttpException('Téléphone non trouvé');

        $this->manager->remove($device);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
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
     * @Route\Get("/own/notifications")
     */
    public function getNotificationsAction()
    {
        $repo = $this->manager->getRepository('KIUserBundle:Notification');
        $user = $this->get('security.context')->getToken()->getUser();

        // On récupère toutes les notifs
        $notifications = $repo->findAll();
        $return = array();

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
                throw new \Exception('Notification : mode d\'envoi inconnu ('.$mode.')');
        }

        // On marque chaque notification récupérée comme lue
        foreach ($return as $notification) {
            $notification->addRead($user);
        }
        $this->manager->flush();

        return $this->restResponse($return);
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
     * @Route\Get("/own/followed")
     */
    public function getFollowedAction()
    {
        return $this->restResponse($this->getFollowedClubs());
    }

    protected function getFollowedClubs($user = null) {
        $repo = $this->manager->getRepository('KIUserBundle:Club');
        if ($user === null)
            $user = $this->get('security.context')->getToken()->getUser();
        $userNotFollowed = $user->getClubsNotFollowed();

        $clubs = $repo->findAll();
        $return = array();
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
     * @Route\Get("/own/events")
     */
    public function getOwnEventsAction()
    {
        $events = $this->getFollowedEvents();

        // Si on prend tout on renvoie comme ça
        if ($this->getRequest()->query->has('all'))
            return $this->restResponse($events);

        $return = array();
        $today = time();

        // On élimine les anciens événements si on ne souhaite pas tout
        foreach ($events as $event) {
            if ($event->getEndDate() > $today)
                $return[] = $event;
        }

        return $this->restResponse($return);
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

            return new \Symfony\Component\HttpFoundation\Response($calStr, 200, array(
                    'Content-Type' => 'text/calendar; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="calendar.ics"',
                )
            );
        }
    }

    // Va chercher les événements suivis
    private function getFollowedEvents($user = null) {
        $repo = $this->manager->getRepository('KIPublicationBundle:Event');

        if ($user === null)
            $user = $this->get('security.context')->getToken()->getUser();

        $followedEvents = $repo->findBy(array('authorClub'=> $this->getFollowedClubs($user)));
        $persoEvents = $repo->findBy(array('authorUser' => $user, 'authorClub' => null));
        $events = array_merge($followedEvents, $persoEvents);

        // Tri et élimination des données
        $dates = array();
        $return = array();
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

    private function getCourseitems($user = null) {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIPublicationBundle:CourseUser');

        if ($user === null)
            $user = $this->get('security.context')->getToken()->getUser();

        // On extraie les Courseitem et on les trie par date de début
        $result = array();
        $timestamp = array();
        foreach ($repo->findBy(array('user' => $user)) as $courseUser) {
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
     * @Route\Get("/own/newsitems")
     */
    public function getNewsItemsAction()
    {
        $repository = $this->manager->getRepository('KIPublicationBundle:Newsitem');

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));
        $findBy['authorClub'] = $this->getFollowedClubs();
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);

        // Tri des données
        $dates = array();
        foreach ($results as $key => $newsitem) {
            $results[$key] = $newsitem;
            $dates[$key] = $newsitem->getDate();
        }
        array_multisort($dates, SORT_DESC, $results);
        return $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);
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
     * @Route\Get("/own/courses")
     */
    public function getOwnCoursesAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIPublicationBundle:CourseUser');

        $return = array();
        foreach ($repo->findBy(array('user' => $this->user)) as $courseUser) {
                    $return[] = array('course' => $courseUser->getCourse(), 'group' => $courseUser->getGroup());
        }

        return $this->restResponse($return);
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
     * @Route\Get("/own/courseitems")
     */
    public function getCourseitemsAction()
    {
        return $this->restResponse($this->getCourseitems());
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
     * @Route\Patch("/own/preferences")
     */
    public function changePreferenceAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!($request->request->has('key') && $request->request->has('value')))
            throw new BadRequestHttpException('Champ manquant');

        if ($user->addPreference($request->request->get('key'), $request->request->get('value'))) {
            $this->manager->flush();
            return $this->restResponse(null, 204);
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
     * @Route\Delete("/own/preferences")
     */
    public function removePreferenceAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!($request->request->has('key')))
            throw new BadRequestHttpException('Champ manquant');

        if ($user->removePreference($request->request->get('key'))) {
            $this->manager->flush();

            return $this->restResponse(null, 204);
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
     * @Route\Get("/own/preferences")
     */
    public function getPreferencesAction()
    {
        $user = $this->user;
        return $this->restResponse($user->getPreferences());
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
     * @Route\Get("/own/token")
     */
    public function getTokenAction()
    {
        return array('token' => $this->get('ki_user.service.token')->getToken());
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
     * @Route\Get("/own/fixs")
     */
    public function getOwnFixsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');

        $repository = $this->manager->getRepository('KIClubinfoBundle:Fix');
        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));

        $findBy['user'] = $user;
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);

        return $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);
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
     * @Route\Get("/own/user")
     */
    public function getOwnUserAction()
    {
        return $this->restResponse($this->user);
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
     * @Route\Post("/own/user")
     */
    public function postOwnUserAction()
    {
        $request = $this->getRequest()->request;
        if (!$request->has('password') || !$request->has('confirm') || !$request->has('old'))
            throw new BadRequestHttpException('Champs password/confirm non rempli(s)');

        if ($this->user->hasRole('ROLE_ADMISSIBLE'))
            return $this->jsonResponse(null, 403);

        // Pour changer le mot de passe on doit passer par le UserManager
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($this->user->getUsername());

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($request->get('old'), $user->getSalt());

        if ($encodedPassword != $user->getPassword())
            throw new BadRequestHttpException('Ancien mot de passe incorrect');

        $user->setPlainPassword($request->get('password'));
        $userManager->updateUser($user, true);

        return $this->restResponse(null, 204);
    }
}
