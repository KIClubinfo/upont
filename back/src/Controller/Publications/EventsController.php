<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Achievement;
use App\Entity\Event;
use App\Entity\EventUser;
use App\Event\AchievementCheckEvent;
use App\Form\EventType;
use App\Listener\EventListener;
use App\Service\NotifyService;
use Carbon\Carbon;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Event::class, EventType::class);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Liste les événements",
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
     * @Route("/events", methods={"GET"})
     */
    public function getEventsAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne un événement",
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
     * @Route("/events/{slug}", methods={"GET"})
     */
    public function getEventAction($slug)
    {
        $event = $this->getOne($slug);

        return $this->json($event);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Crée un événement",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="text",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startDate",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endDate",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="entryMethod",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunDate",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunLimit",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunText",
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
     *     @SWG\Parameter(
     *         name="sendMail",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="authorClub",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="uploadedFiles",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Modifie un événement",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="text",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="startDate",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="endDate",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="entryMethod",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunDate",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunLimit",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="shotgunText",
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
     *     @SWG\Parameter(
     *         name="sendMail",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="authorClub",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="uploadedFiles",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="file",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime un événement",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Shotgunne un événement",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Modifie un shotgun",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Enlève un utilisateur du shotgun",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne le tableau des utilisateurs ayant réussi le shotgun ainsi que la liste d'attente classée par date de shotgun et le nombre limite de personnes pouvant shotgunner.",
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
            if (Carbon::now() >= $event->getShotgunDate()) {
                $result['success'] = $success;
                $result['fail'] = $fail;
            } else {
                $result['success'] = [];
                $result['fail'] = [];
            }
        }

        if ($this->is('ADMIN')) {
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Ajoute un utilisateur à l'event",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retire la demande d'inscription",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Ajoute un utilisateur qui ne vient pas à l'event",
     *     @SWG\Response(
     *         response="200",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retire la demande de désinscription",
     *     @SWG\Response(
     *         response="200",
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
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne la liste des participants à l'événement",
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
     * @Route("/events/{slug}/attendees", methods={"GET"})
     */
    public function getAttendeesAction($slug)
    {
        return $this->json($this->findBySlug($slug)->getAttendees());
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne la liste des non-participants à l'événement",
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
     * @Route("/events/{slug}/pookies", methods={"GET"})
     */
    public function getPookiesAction($slug)
    {
        return $this->json($this->findBySlug($slug)->getPookies());
    }
}
