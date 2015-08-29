<?php

namespace KI\PublicationBundle\Listener;

use KI\PublicationBundle\Entity\Event;
use KI\UserBundle\Service\MailerService;
use KI\UserBundle\Service\NotifyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\Club;
use Doctrine\ORM\EntityRepository;

class EventListener
{
    protected $notifyService;
    protected $mailerService;
    protected $dispatcher;
    protected $userRepository;

    public function __construct(NotifyService $notifyService,
                                MailerService $mailerService,
                                EventDispatcherInterface $dispatcher,
                                EntityRepository $userRepository)
    {
        $this->notifyService  = $notifyService;
        $this->mailerService  = $mailerService;
        $this->dispatcher     = $dispatcher;
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

            list($usersPush, $usersMail) = $this->getUsersToNotify($club);

            $vars = array(
                'event' => $event,
                'start' => ucfirst(strftime('%a %d %B à %Hh%M', $event->getStartDate())),
                'end'   => ucfirst(strftime('%a %d %B à %Hh%M', $event->getEndDate()))
            );

            $shotgunPrefix = '';
            if (!empty($event->getShotgunDate())) {
                $vars['shotgun'] = ucfirst(strftime('%a %d %B à %Hh%M', $event->getShotgunDate()));
                $shotgunPrefix = '[SHOTGUN]';
            }

            $title = '['.$club->getName().']'.$shotgunPrefix.' '.$event->getName();
            $this->mailerService->send($usersMail, $title, 'KIPublicationBundle::invitation.html.twig', $vars);

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

        // Si ce n'est pas un event perso, on notifie les utilisateurs des changements
        if ($club) {
            $modifications = array();

            if ($event->getStartDate() != $oldEvent->getStartDate() || $event->getEndDate() != $oldEvent->getEndDate()) {
                $modifications['start'] = ucfirst(strftime('%a %d %B à %Hh%M', $event->getStartDate()));
                $modifications['end']   = ucfirst(strftime('%a %d %B à %Hh%M', $event->getEndDate()));
            }
            if ($event->getShotgunDate() != $oldEvent->getShotgunDate()) {
                $modifications['shotgun'] = ucfirst(strftime('%a %d %B à %Hh%M', $event->getShotgunDate()));
            }
            if ($event->getPlace() != $oldEvent->getPlace()) {
                $modifications['place'] = $event->getPlace();
            }

            if (empty($modifications)) {
                return;
            }
            list($usersPush, $usersMail) = $this->getUsersToNotify($club, true);
            $modifications['event'] = $event;

            $title = '['.$club->getName().'][MODIFICATION] '.$event->getName();
            $this->mailerService->send($usersMail, $title, 'KIPublicationBundle::modification.html.twig', $modifications);
        }
    }

    /**
     * Retourne les utilisateurs que l'on peut notifier par Push et/ou Mail
     * @param  Club  $club   Le club servant à déterminer l'égilibilité
     * @param  bool  $modify Si le mail concerne la modification d'un événement
     * @return array
     */
    private function getUsersToNotify(Club $club, $modify = false)
    {
        $allUsers = $this->userRepository->findAll();
        $usersPush = $usersMail = array();

        foreach ($allUsers as $candidate) {
            if (!$candidate->getClubsNotFollowed()->contains($club)) {
                $usersPush[] = $candidate;

                if ($candidate->getMailEvent() && (!$modify || $candidate->getMailModification())) {
                    $usersMail[] = $candidate;
                }
            }
        }
        return array($usersPush, $usersMail);
    }
}
