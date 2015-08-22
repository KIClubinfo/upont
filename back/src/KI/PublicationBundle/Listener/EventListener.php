<?php

namespace KI\PublicationBundle\Listener;

use KI\PublicationBundle\Entity\Event;
use KI\UserBundle\Service\NotifyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Entity\Achievement;
use Doctrine\ORM\EntityRepository;

class EventListener
{
    protected $notifyService;
    protected $dispatcher;
    protected $userRepository;

    public function __construct(NotifyService $notifyService,
                                EventDispatcherInterface $dispatcher,
                                EntityRepository $userRepository)
    {
        $this->notifyService  = $notifyService;
        $this->dispatcher     = $dispatcher;
        $this->userRepository = $userRepository;
    }

    public function postPersist(Event $entity)
    {
        $club = $entity->getAuthorClub();

        // Si ce n'est pas un event perso, on notifie les utilisateurs suivant le club
        if ($club) {
            $achievementCheck = new AchievementCheckEvent(Achievement::EVENT_CREATE);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            $allUsers = $this->userRepository->findAll();
            $users = array();

            foreach ($allUsers as $candidate) {
                if ($candidate->getClubsNotFollowed()->contains($club)) {
                    $users[] = $candidate;
                }
            }

            $text = substr($entity->getText(), 0, 140).'...';
            $this->notifyService->notify(
                'notif_followed_event',
                $entity->getName(),
                $text,
                'exclude',
                $users
            );
        }
    }
}
