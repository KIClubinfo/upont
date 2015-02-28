<?php

namespace KI\UpontBundle\Controller\Publications;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UpontBundle\Entity\Publications\EventUser;

class EventsController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Event', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les événements",
     *  output="KI\UpontBundle\Entity\Publications\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getEventsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un événement",
     *  output="KI\UpontBundle\Entity\Publications\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getEventAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée un événement",
     *  input="KI\UpontBundle\Form\Publications\EventType",
     *  output="KI\UpontBundle\Entity\Publications\Event",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function postEventAction()
    {
        $return = $this->partialPost($this->checkClubMembership());

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $return['item']->setDate(time());
            $return['item']->setAuthorUser($this->container->get('security.context')->getToken()->getUser());

            $club = $return['item']->getAuthorClub();

            // Si ce n'est pas un event perso, on notifie les utilisateurs suivant le club
            if ($club) {
                $allUsers = $this->em->getRepository('KIUpontBundle:Users\User')->findAll();
                $users = array();

                foreach ($allUsers as $candidate) {
                    if (!$candidate->getClubsNotFollowed()->contains($club))
                        $users[] = $candidate;
                }

                $text = $return['item']->getTextShort() !== null ? $return['item']->getTextShort() : '';
                $this->notify(
                    'notif_followed_event',
                    $return['item']->getName(),
                    $text,
                    'to',
                    $users
                );
            }
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un événement",
     *  input="KI\UpontBundle\Form\Publications\EventType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function patchEventAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->patch($slug, $this->checkClubMembership($club));
    }

    /**
     * @ApiDoc(
     *  description="Supprime un événement",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function deleteEventAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->delete($slug, $this->checkClubMembership($club));
    }

    /**
     * @ApiDoc(
     *  description="Shotgunne un événement",
     *  requirements={
     *   {
     *    "name"="motivation",
     *    "dataType"="string",
     *    "description"="Un texte de motivation"
     *   }
     *  },
     *  output="KI\UpontBundle\Entity\Publications\EventUser",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/events/{slug}/shotgun")
     */
    public function postEventUserAction($slug)
    {
        $request = $this->getRequest()->request;

        if (!$request->has('motivation'))
            throw new BadRequestHttpException('Texte de motivation manquant');

        // On vérifie que l'utilisateur n'a pas déjà shotguné
        $event = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();

        $repo = $this->em->getRepository('KIUpontBundle:Publications\EventUser');
        $userEvent = $repo->findBy(array('event' => $event, 'user' => $user));
        if (count($userEvent) != 0)
            return;

        $userEvent = new EventUser();
        $userEvent->setEvent($event);
        $userEvent->setUser($user);
        $userEvent->setDate(time());
        $userEvent->setMotivation($request->get('motivation'));

        $this->em->persist($userEvent);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un shotgun",
     *  requirements={
     *   {
     *    "name"="motivation",
     *    "dataType"="string",
     *    "description"="Un texte de motivation"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Patch("/events/{slug}/shotgun")
     */
    public function patchEventUserAction($slug)
    {

        $request = $this->getRequest()->request;

        if (!$request->has('motivation'))
            throw new BadRequestHttpException('Texte de motivation manquant');

        $repo = $this->em->getRepository('KIUpontBundle:Publications\EventUser');
        $event = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();

        $userEvent = $repo->findBy(array('event' => $event, 'user' => $user));

        if (count($userEvent) == 1) {
            $userEvent[0]->setMotivation($request->get('motivation'));
            $this->em->flush();
        } else {
            throw new NotFoundHttpException('Participation au shotgun non trouvée');
        }

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève un utilisateur du shotgun",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/events/{slug}/shotgun")
     */
    public function deleteEventUserAction($slug) {

        $repo = $this->em->getRepository('KIUpontBundle:Publications\EventUser');
        $event = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();

        $userEvent = $repo->findBy(array('event' => $event, 'user' => $user));

        if (count($userEvent) == 1) {
            $event = $userEvent[0]->getEvent();

            // On regarde si une place s'est libérée pour quelqu'un, au cas où
            // on le prévient
            $userEvents = $repo->findBy(array('event' => $event));

            if (isset($userEvents[$event->getShotgunLimit()])) {
                $this->notify(
                    'notif_shotgun_freed',
                    $event->getName(),
                    'Des places de shotgun se sont libérées, tu as maintenant accès à l\'événément !',
                    'to',
                    $userEvents[$event->getShotgunLimit()]->getUser()
                );
            }

            $this->em->remove($userEvent[0]);
            $this->em->flush();
        } else {
            throw new NotFoundHttpException('Participation au shotgun non trouvée');
        }
        return $this->jsonResponse(null, 204); }

    /**
     * @ApiDoc(
     *  description="Retourne le tableau des utilisateurs ayant réussi le shotgun ainsi que la liste d'attente classée par date de shotgun et le nombre limite de personnes pouvant shotgunner.",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Get("/events/{slug}/shotgun")
     */
    public function getEventUserAction($slug) {

        $repo = $this->em->getRepository('KIUpontBundle:Publications\EventUser');
        $event = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();

        $userEvent = $repo->findBy(array('event' => $event), array('date' => 'ASC'));

        $position = -1;
        $limit = $event->getShotgunLimit();

        $fail = $success = $shotgun = array();
        $count = min(count($userEvent), $limit);

        for ($i = 0; $i < $count; $i++) {
            // Si le shotgun a été fait avant la date prévue, on passe
            if ($userEvent[$i]->getDate() < $event->getShotgunDate()) {
                $position = 0;
                continue;
            }

            if ($user == $userEvent[$i]->getUser())
                $position = $i + 1;
            $shotgun['user'] = $userEvent[$i]->getUser();
            $shotgun['date'] = $userEvent[$i]->getDate();
            if ($user == $event->getAuthorUser()) {
                $shotgun['motivation'] = $userEvent[$i]->getMotivation();
            }
            $success[$i] = $shotgun;
        }

        $count = count($userEvent);
        for ($i = $limit; $i < $count; $i++) {
            if ($user == $userEvent[$i]->getUser())
                $position = $i + 1;
            $shotgun['user'] = $userEvent[$i]->getUser();
            $shotgun['date'] = $userEvent[$i]->getDate();

            if ($user == $event->getAuthorUser())
                $shotgun['motivation'] = $userEvent[$i]->getMotivation();
            $fail[$i] = $shotgun;
        }

        $result = array(
            'status'  => $position <= $limit && $position > 0,
            'limit'   => $limit,
            'date'    => $event->getShotgunDate()
        );

        // Si on est l'auteur du shotgun, on peut récupérer la liste d'attente
        if ($event->getAuthorUser() == $user) {
            $result['succcess'] = $success;
            $result['fail'] = $fail;
        }

        if ($position != -1) {
            $result['position'] = $position;
        }
        if ($position <= $limit && $position > 0) {
            $result['shotgunText'] = $event->getShotgunText();
        }
        else if ($position > $limit) {
            // La liste d'attente commence à 0.
            $result['waitingList'] = $position - $limit;
        }

        return $this->restResponse($result);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur à l'event",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/events/{slug}/attend")
     */
    public function attendAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $event = $this->findBySlug($slug);

        if ($event->getAttendees()->contains($user)) {
            throw new BadRequestHttpException('Vous participez déjà à cet évènement');
        } else {
            // On enlève éventuellement l'utilisateur des pookies
            if ($event->getPookies()->contains($user))
                $event->removePookie($user);

            $event->addAttendee($user);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retire la demande d'inscription",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/events/{slug}/attend")
     */
    public function noAttendAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $event = $this->findBySlug($slug);

        if (!$event->getAttendees()->contains($user)) {
            throw new BadRequestHttpException('Vous ne participez pas à cet évènement');
        } else {
            $event->removeAttendee($user);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur qui ne vient pas à l'event",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/events/{slug}/decline")
     */
    public function addPookieAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $event = $this->findBySlug($slug);

        if ($event->getPookies()->contains($user)) {
            throw new BadRequestHttpException('Vous ne participez déjà pas à cet évènement');
        } else {
            // On ne fait plus participer l'utilisateur s'il participait auparavent
            if ($event->getAttendees()->contains($user))
                $event->removeAttendee($user);

            $event->addPookie($user);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retire la demande de désinscription",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/events/{slug}/decline")
     */
    public function removePookieAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $event = $this->findBySlug($slug);

        if (!$event->getPookies()->contains($user)) {
            throw new BadRequestHttpException('Vous ne vous êtes pas désinscrit de cet évènement');
        } else {
            $event->removePookie($user);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des participants à l'événement",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Get("/events/{slug}/attendees")
     */
    public function getAttendeesAction($slug) { return $this->restResponse($this->findBySlug($slug)->getAttendees()); }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des non-participants à l'événement",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Get("/events/{slug}/pookies")
     */
    public function getPookiesAction($slug) { return $this->restResponse($this->findBySlug($slug)->getPookies()); }
}
