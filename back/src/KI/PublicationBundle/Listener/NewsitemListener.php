<?php

namespace KI\PublicationBundle\Listener;

use KI\PublicationBundle\Entity\Newsitem;
use KI\UserBundle\Service\NotifyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Entity\Achievement;
use Doctrine\ORM\EntityRepository;

class NewsitemListener
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

    public function postPersist(Newsitem $entity)
    {
        $club = $entity->getAuthorClub();
        $text = substr($entity->getText(), 0, 140).'...';

        // Si ce n'est pas un message perso, on notifie les utilisateurs suivant le club
        if ($club) {
            $achievementCheck = new AchievementCheckEvent(Achievement::NEWS_CREATE);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            $allUsers = $this->userRepository->findAll();
            $users = array();

            foreach ($allUsers as $candidate) {
                if ($candidate->getClubsNotFollowed()->contains($club)) {
                    $users[] = $candidate;
                }
            }

            $this->notifyService->notify(
                'notif_followed_news',
                $entity->getName(),
                $text,
                'exclude',
                $users
            );
        } else {
            // Si c'est une news perso on notifie tous ceux qui ont envie
            $this->notifyService->notify(
                'notif_news_perso',
                $entity->getName(),
                $text,
                'exclude',
                array()
            );
        }
    }
}
