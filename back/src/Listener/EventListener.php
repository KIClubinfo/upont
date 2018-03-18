<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use App\Entity\Event;
use App\Entity\Achievement;
use App\Entity\Club;
use App\Event\AchievementCheckEvent;
use App\Repository\UserRepository;
use App\Service\MailerService;
use App\Service\NotifyService;

class EventListener
{
    protected $dispatcher;
    protected $mailerService;
    protected $notifyService;
    protected $userRepository;

    public function __construct(EventDispatcherInterface $dispatcher,
                                MailerService $mailerService,
                                NotifyService $notifyService,
                                userRepository $userRepository)
    {
        $this->dispatcher     = $dispatcher;
        $this->mailerService  = $mailerService;
        $this->notifyService  = $notifyService;
        $this->userRepository = $userRepository;
    }

    public function postPersist(Event $event)
    {
        $club = $event->getAuthorClub();

        if ($event->getEntryMethod() === Event::TYPE_FERIE) {
            return;
        }

        // Si ce n'est pas un event perso, on notifie les utilisateurs suivant le club
        if ($club) {
            $achievementCheck = new AchievementCheckEvent(Achievement::EVENT_CREATE);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            list($usersPush, $usersMail) = $this->getUsersToNotify($club, false, $event->getSendMail());

            $vars = [
                'post' => $event,
                'start' => $event->getStartDate()->formatLocalized('%A %d %B à %Hh%M'),
                'end'   => $event->getEndDate()->formatLocalized('%A %d %B à %Hh%M'),
            ];

            $shotgunPrefix = '';
            if (!empty($event->getShotgunDate())) {
                $vars['shotgun'] = $event->getShotgunDate()->formatLocalized('%A %d %B à %Hh%M');
                $shotgunPrefix = '[SHOTGUN]';
            }

            $attachments = [];
            foreach ($event->getFiles() as $file) {
                $attachments[] = ["path" => $file->getAbsolutePath(), "name" => $file->getName()];
            }

            $title = '['.$club->getName().']'.$shotgunPrefix.' '.$event->getName();
            $this->mailerService->send($event->getAuthorUser(),
                $usersMail,
                $title,
                'invitation.html.twig',
                $vars,
                $attachments
            );

            $text = substr($event->getText(), 0, 140).'...';
            $this->notifyService->notify(
                'notif_followed_event',
                $event->getName(),
                $text,
                'to',
                $usersPush
            );
        }
    }

    public function postUpdate(Event $event, Event $oldEvent)
    {
        $club = $event->getAuthorClub();

        if ($event->getEntryMethod() === Event::TYPE_FERIE) {
            return;
        }

        // Si ce n'est pas un event perso
        // On notifie les utilisateurs des changements
        if ($club) {
            $modifications = [];

            if ($event->getStartDate() != $oldEvent->getStartDate()
                || $event->getEndDate() != $oldEvent->getEndDate()
                ) {
                $modifications['start'] = $event->getStartDate()->formatLocalized('%A %d %B à %Hh%M');
                $modifications['end']   = $event->getEndDate()->formatLocalized('%A %d %B à %Hh%M');
            }

            if ($event->getShotgunDate() != $oldEvent->getShotgunDate()) {
                $modifications['shotgun'] = $event->getShotgunDate()->formatLocalized('%A %d %B à %Hh%M');
            }

            if ($event->getPlace() != $oldEvent->getPlace()) {
                $modifications['place'] = $event->getPlace();
            }

            if (empty($modifications)) {
                return;
            }
            list($usersPush, $usersMail) = $this->getUsersToNotify($club, true, $event->getSendMail());
            $modifications['post'] = $event;

            $title = '['.$club->getName().'][MODIFICATION] '.$event->getName();
            $this->mailerService->send($event->getAuthorUser(),
                $usersMail,
                $title,
                'modification.html.twig',
                $modifications);
        }
    }

    /**
     * Retourne les utilisateurs que l'on peut notifier par Push et/ou Mail
     * @param  Club  $club   Le club servant à déterminer l'égilibilité
     * @param  bool  $modify Si le mail concerne la modification d'un événement
     * @return array
     */
    private function getUsersToNotify(Club $club, $modify = false, $sendMail = true)
    {
        $allUsers = $this->userRepository->findAll();
        $usersPush = $usersMail = [];

        foreach ($allUsers as $candidate) {
            if (!$candidate->getClubsNotFollowed()->contains($club)) {
                $usersPush[] = $candidate;

                if ($sendMail
                    && $candidate->getMailEvent()
                    && (!$modify || $candidate->getMailModification())
                    ) {
                    $usersMail[] = $candidate;
                }
            }
        }
        return [$usersPush, $usersMail];
    }
}
