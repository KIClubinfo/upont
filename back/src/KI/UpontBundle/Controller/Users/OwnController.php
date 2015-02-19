<?php

namespace KI\UpontBundle\Controller\Users;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use KI\UpontBundle\Entity\Users\Device;
use KI\UpontBundle\Entity\Achievement;
use KI\UpontBundle\Entity\Notification;


class OwnController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'Users');
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
     */
    public function getAchievementsAction()
    {
        $repoA = $this->em->getRepository('KIUpontBundle:Achievement');
        $repoAU = $this->em->getRepository('KIUpontBundle:AchievementUser');
        $user = $this->get('security.context')->getToken()->getUser();

        $unlocked = array();
        $oUnlocked = array();
        $response = $repoAU->findByUser($user);
        foreach($response as $achievementUser) {
            $achievement = $achievementUser->getAchievement();
            $unlocked[] = array(
                'name'        => $achievement->name(),
                'description' => $achievement->description(),
                'points'      => $achievement->points(),
                'image'       => $achievement->image(),
                'date'        => $achievementUser->getDate(),
                'ownedBy'     => count($repoAU->findByAchievement($achievement)),
            );
            $oUnlocked[] = $achievement;
        }
        $all = $repoA->findAll();
        $locked = array();
        $points = 0;
        $factor = 1;

        // On regarde quels achievements sont locked et on en profite pour
        // calculer le nombre de points de l'utilisateur obtenus par les
        // achievements
        foreach($all as $achievement) {
            if(!in_array($achievement, $oUnlocked)) {
                $locked[] = array(
                    'name'        => $achievement->name(),
                    'description' => $achievement->description(),
                    'points'      => $achievement->points(),
                    'image'       => $achievement->image(),
                    'ownedBy'     => count($repoAU->findByAchievement($achievement)),
                );
            } else {
                if(gettype($achievement->points()) == 'integer') {
                    $points += $achievement->points();
                } else if($achievement->points() == '+10%') {
                    $factor += 0.1;
                } else if($achievement->points() == '+15%') {
                    $factor += 0.15;
                } else if($achievement->points() == '+75%') {
                    $factor += 0.75;
                }
            }
        }

        // On renvoie pas mal de données utiles
        $response = Achievement::getLevel($points);
        $return = array(
            'number'        => $response['number'],
            'points'        => ceil($factor*$points),
            'current_level' => $response['current'],
            'next_level' => isset($response['next']) ? $response['next'] : null,
            'unlocked'      => $unlocked,
            'locked'        => $locked,
        );

        return $this->jsonResponse($return);
    }

    /**
     * @ApiDoc(
     *  description="Enregistre un smartphone auprès de l'API",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
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
        $repo = $this->em->getRepository('KIUpontBundle:Users\Device');
        $devices = $repo->findByDevice($request->get('device'));
        if (!empty($devices))
            throw new BadRequestHttpException('Téléphone déjà enregistré');

        $device = new Device();
        $device->setOwner($this->get('security.context')->getToken()->getUser());
        $device->setDevice($request->get('device'));
        $device->setType($request->get('type'));
        $this->em->persist($device);
        $this->em->flush();

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
     */
    public function deleteDeviceAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $repo = $this->em->getRepository('KIUpontBundle:Users\Device');
        $device = $repo->findOneByDevice(str_replace('"', '', $id));

        if ($device === null)
            throw new NotFoundHttpException('Téléphone non trouvé');

        $this->em->remove($device);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie les notifications non lues de l'utilisateur actuel",
     *  output="KI\UpontBundle\Entity\Notification",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getNotificationsAction()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Notification');
        $user = $this->get('security.context')->getToken()->getUser();

        // On récupère toutes les notifs
        $notifications = $repo->findAll();
        $return = array();

        // On filtre celles qui sont uniquement destinées à l'utilisateur actuel
        foreach($notifications as $notification) {
            $mode = $notification->getMode();
            if($mode == 'to') {
                // Si la notification n'a pas été lue
                if ($notification->getRecipient()->contains($user) && !$notification->getRead()->contains($user))
                    $return[] = $notification;
            }
            else if($mode == 'exclude') {
                // Si la notification n'a pas été lue
                if (!$notification->getRead()->contains($user) && !$notification->getRecipient()->contains($user))
                    $return[] = $notification;
            }
            else
                throw new \Exception('Notification : mode d\'envoi inconnu (' . $mode . ')');
        }

        return $this->restResponse($return);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des clubs suivis",
     *  output="KI\UpontBundle\Entity\Users\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getFollowedAction()
    {
        return $this->restResponse($this->getFollowedClubs());
    }

    protected function getFollowedClubs() {
        $repo = $this->em->getRepository('KIUpontBundle:Users\Club');
        $user = $this->get('security.context')->getToken()->getUser();
        $userNotFollowed = $user->getClubsNotFollowed();

        $clubs = $repo->findAll();
        $return = array();
        foreach($clubs as $club) {
            if (!$userNotFollowed->contains($club)) {
                $return[] = $club;
            }
        }

        return $return;
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des évènements suivis et persos",
     *  output="KI\UpontBundle\Entity\Publications\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getEventsAction()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Publications\Event');
        $user = $this->get('security.context')->getToken()->getUser();

        $followedEvents = $repo->findBy(array('authorClub'=> $this->getFollowedClubs()));
        $persoEvents = $repo->findBy(array('authorUser' => $user, 'authorClub' => null));
        $events = array_merge($followedEvents, $persoEvents);

        // Tri et élimination des données
        $dates = array();
        $return = array();
        $today = mktime(0, 0, 0);
        foreach ($events as $key => $event) {
            if ($event->getStartDate() > $today) {
                $return[$key] = $event;
                $dates[$key] = $event->getStartDate();
            }
        }
        array_multisort($dates, SORT_DESC, $return);
        return $this->restResponse($return);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des sondages relatifs aux clubs suivis",
     *  output="KI\UpontBundle\Entity\Publications\Poll",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  tags={
     *    "TODO"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getPollsAction()
    {
        $polls = array();

        // Traitement TODO CBo15
        return $this->restResponse($polls);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des news suivies",
     *  output="KI\UpontBundle\Entity\Publications\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getNewsItemsAction()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Publications\Newsitem');

        $followedNews = $repo->findBy(array('authorClub'=> $this->getFollowedClubs()));

        // Tri des données
        $dates = array();
        foreach ($followedNews as $key => $newsitem) {
            $dates[$key] = $newsitem->getDate();
        }
        array_multisort($dates, SORT_DESC, $followedNews);
        return $this->restResponse($followedNews);
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des cours suivis",
     *  output="KI\UpontBundle\Entity\Publications\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getCoursesAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->restResponse($user->getCourses());
    }

    /**
     * @ApiDoc(
     *  description="Renvoie la liste des cours suivis qui auront lieu bientôt, et leur salle",
     *  output="KI\UpontBundle\Entity\Publications\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getCoursesitemsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->restResponse($user->getCourses());
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
     * {
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
     */
    public function changePreferenceAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        if (!($request->request->has('key') && $request->request->has('value')))
            throw new BadRequestHttpException();

        if ($user->addPreference($request->request->get('key'), $request->request->get('value'))) {
            $this->em->flush();
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
     */
    public function removePreferenceAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        if (!($request->request->has('key')))
            throw new BadRequestHttpException();

        if ($user->removePreference($request->request->get('key'))) {
            $this->em->flush();

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
     */
    public function getPreferencesAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->jsonResponse($user->getPreferences());
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
    public function getOwnCalendarAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $calStr = $this->get('ki_upont.calendar')->getCalendar($user);

        return $this->restResponse($calStr, 200, array(
                'Content-Type'        => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="calendar.ics"',
            )
        );
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
     */
    public function getTokenAction()
    {
        return $this->get('ki_upont.token')->getToken();
    }
}
