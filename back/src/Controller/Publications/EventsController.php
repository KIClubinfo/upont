<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Event;
use App\Entity\EventUser;
use App\Entity\Achievement;
use App\Event\AchievementCheckEvent;
use App\Form\EventType;
use App\Listener\EventListener;
use App\Service\NotifyService;
use Carbon\Carbon;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Event::class, EventType::class);
    }

    /**
     * @ApiDoc(
     *  description="Retourne un événement",
     *  output="App\Entity\Event",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}", methods={"GET"})
     */
    public function getEventAction($slug)
    {
        $event = $this->getOne($slug);

        return $this->json($event);
    }

    /**
     * @ApiDoc(
     *  description="Crée un événement",
     *  input="App\Form\EventType",
     *  output="App\Entity\Event",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/events", methods={"POST"})
     */
    public function postEventAction(EventListener $eventListener)
    {
        $data = $this->post($this->isClubMember());

        if ($data['code'] == 201) {
            $eventListener->postPersist($data['item']);
        }

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un événement",
     *  input="App\Form\EventType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}", methods={"PATCH"})
     */
    public function patchEventAction(EventListener $eventListener, $slug)
    {
        $item = $this->findBySlug($slug);
        $oldItem = clone $item;

        $club = $item->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        $data = $this->patch($slug, $this->isClubMember($club));
        $eventListener->postUpdate($item, $oldItem);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un événement",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}", methods={"DELETE"})
     */
    public function deleteEventAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        $event = $this->findBySlug($slug);

        // On n'oublie pas de supprimer tous les shotguns éventuellement associés
        $repo = $this->manager->getRepository(EventUser::class);
        $userEvent = $repo->findByEvent($event);

        foreach ($userEvent as $item) {
            $this->manager->remove($item);
        }

        $this->delete($slug, $this->isClubMember($club));

        return $this->json(null, 204);
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
     *  output="App\Entity\EventUser",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/shotgun", methods={"POST"})
     */
    public function postEventUserAction(Request $request, $slug)
    {
        $event = $this->findBySlug($slug);

        if ($event->getEntryMethod() != Event::TYPE_SHOTGUN)
            throw new BadRequestHttpException('Ce n\'est pas un événement à shotgun !');

        if (!$request->request->has('motivation'))
            throw new BadRequestHttpException('Texte de motivation manquant');

        $repo = $this->manager->getRepository(EventUser::class);
        $user = $this->user;
        $userEvent = $repo->findBy(['event' => $event, 'user' => $user]);

        // On vérifie que l'utilisateur n'a pas déjà shotguné
        if (count($userEvent) != 0)
            throw new BadRequestHttpException('Tu es déjà inscrit !');

        //S'il est l'heure, on accepte le shotgun
        if (Carbon::now() >= $event->getShotgunDate()) {
            $userEvent = new EventUser();
            $userEvent->setEvent($event);
            $userEvent->setUser($user);
            $userEvent->setDate(Carbon::now());
            $userEvent->setMotivation($request->request->get('motivation'));

            $this->manager->persist($userEvent);
            $this->manager->flush();
        }

        return $this->json(null, 204);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/shotgun", methods={"PATCH"})
     */
    public function patchEventUserAction(Request $request, $slug)
    {
        $event = $this->findBySlug($slug);
        if ($event->getEntryMethod() != Event::TYPE_SHOTGUN)
            throw new BadRequestHttpException('Ce n\'est pas un événement à shotgun !');

        if (!$request->request->has('motivation'))
            throw new BadRequestHttpException('Texte de motivation manquant');

        $repo = $this->manager->getRepository(EventUser::class);
        $user = $this->user;
        $userEvent = $repo->findBy(['event' => $event, 'user' => $user]);

        if (count($userEvent) == 1) {
            $userEvent[0]->setMotivation($request->request->get('motivation'));
            $this->manager->flush();
        } else {
            throw new NotFoundHttpException('Participation au shotgun non trouvée');
        }

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève un utilisateur du shotgun",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/shotgun", methods={"DELETE"})
     */
    public function deleteEventUserAction(NotifyService $notifyService, $slug)
    {
        $event = $this->findBySlug($slug);

        $repo = $this->manager->getRepository(EventUser::class);
        $user = $this->user;
        $userEvent = $repo->findBy(['event' => $event, 'user' => $user]);

        if (count($userEvent) == 1) {
            $event = $userEvent[0]->getEvent();

            // On regarde si une place s'est libérée pour quelqu'un, au cas où
            // on le prévient
            $userEvents = $repo->findBy(['event' => $event], ['date' => 'ASC']);

            if (isset($userEvents[$event->getShotgunLimit()])) {
                $notifyService->notify(
                    'notif_shotgun_freed',
                    $event->getName(),
                    'Des places de shotgun se sont libérées, tu as maintenant accès à l\'événément !',
                    'to',
                    $userEvents[$event->getShotgunLimit()]->getUser()
                );
            }

            $this->manager->remove($userEvent[0]);
            $this->manager->flush();
        } else {
            throw new NotFoundHttpException('Participation au shotgun non trouvée');
        }
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Retourne le tableau des utilisateurs ayant réussi le shotgun ainsi que la liste d'attente classée par date de shotgun et le nombre limite de personnes pouvant shotgunner.",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/shotgun", methods={"GET"})
     */
    public function getEventUserAction($slug)
    {
        $event = $this->findBySlug($slug);

        $repo = $this->manager->getRepository(EventUser::class);
        $user = $this->user;
        $userEvent = $repo->findBy(['event' => $event], ['date' => 'ASC']);

        $position = 0;
        $limit = $event->getShotgunLimit();

        $fail = $success = $shotgun = [];
        $count = min(count($userEvent), $limit);

        for ($i = 0; $i < $count; $i++) {
            if ($user == $userEvent[$i]->getUser())
                $position = $i + 1;

            $shotgun['user'] = $userEvent[$i]->getUser();
            $shotgun['date'] = $userEvent[$i]->getDate();

            if ($user == $event->getAuthorUser()) {
                $shotgun['motivation'] = $userEvent[$i]->getMotivation();
            }
            $success[] = $shotgun;
        }

        $count = count($userEvent);
        for ($i = $limit; $i < $count; $i++) {
            if ($user == $userEvent[$i]->getUser())
                $position = $i + 1;

            $shotgun['user'] = $userEvent[$i]->getUser();
            $shotgun['date'] = $userEvent[$i]->getDate();

            if ($user == $event->getAuthorUser()) {
                $shotgun['motivation'] = $userEvent[$i]->getMotivation();
            }

            $fail[] = $shotgun;
        }

        $result = [
            'status' => $position <= $limit && $position > 0,
            'limit' => $limit,
            'date' => $event->getShotgunDate()
        ];

        // Si on est l'auteur du shotgun, on peut récupérer la liste d'attente
        if ($event->getAuthorUser() == $user) {
            // It's a trap
            if (Carbon::now() >= $event->getShotgunDate()){
                $result['success'] = $success;
                $result['fail'] = $fail;
            } else {
                $result['success'] = [];
                $result['fail'] = [];
            }
        }

        if ($this->is('ADMIN')){
            $result['success'] = $success;
            $result['fail'] = $fail;
        }

        if ($position != 0) {
            $result['position'] = $position;
        }
        if ($position <= $limit && $position > 0) {
            $result['shotgunText'] = $event->getShotgunText();
        } else if ($position > $limit) {
            // La liste d'attente commence à 0.
            $result['waitingList'] = $position - $limit;
        }

        return $this->json($result);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur à l'event",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/attend", methods={"POST"})
     */
    public function attendAction($slug)
    {
        $user = $this->user;
        $event = $this->findBySlug($slug);

        if ($event->getAttendees()->contains($user)) {
            throw new BadRequestHttpException('Vous participez déjà à cet évènement');
        } else {
            // On enlève éventuellement l'utilisateur des pookies
            if ($event->getPookies()->contains($user))
                $event->removePookie($user);

            $event->addAttendee($user);
            $this->manager->flush();

            $dispatcher = $this->container->get('event_dispatcher');
            $achievementCheck = new AchievementCheckEvent(Achievement::EVENT_ATTEND);
            $dispatcher->dispatch('upont.achievement', $achievementCheck);

            return $this->json(null, 204);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/attend", methods={"DELETE"})
     */
    public function noAttendAction($slug)
    {
        $user = $this->user;
        $event = $this->findBySlug($slug);

        if (!$event->getAttendees()->contains($user)) {
            throw new BadRequestHttpException('Vous ne participez pas à cet évènement');
        } else {
            $event->removeAttendee($user);
            $this->manager->flush();

            return $this->json(null, 204);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/decline", methods={"POST"})
     */
    public function addPookieAction($slug)
    {
        $user = $this->user;
        $event = $this->findBySlug($slug);

        if ($event->getPookies()->contains($user)) {
            throw new BadRequestHttpException('Vous ne participez déjà pas à cet évènement');
        } else {
            // On ne fait plus participer l'utilisateur s'il participait auparavent
            if ($event->getAttendees()->contains($user))
                $event->removeAttendee($user);

            $event->addPookie($user);
            $this->manager->flush();

            return $this->json(null, 204);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/decline", methods={"DELETE"})
     */
    public function removePookieAction($slug)
    {
        $user = $this->user;
        $event = $this->findBySlug($slug);

        if (!$event->getPookies()->contains($user)) {
            throw new BadRequestHttpException('Vous ne vous êtes pas désinscrit de cet évènement');
        } else {
            $event->removePookie($user);
            $this->manager->flush();

            return $this->json(null, 204);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/attendees", methods={"GET"})
     */
    public function getAttendeesAction($slug)
    {
        return $this->json($this->findBySlug($slug)->getAttendees());
    }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des non-participants à l'événement",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/events/{slug}/pookies", methods={"GET"})
     */
    public function getPookiesAction($slug)
    {
        return $this->json($this->findBySlug($slug)->getPookies());
    }
}
