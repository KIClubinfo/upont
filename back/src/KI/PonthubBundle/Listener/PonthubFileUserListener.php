<?php

namespace KI\PonthubBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\PonthubBundle\Entity\PonthubFileUser;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PonthubFileUserListener
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PonthubFileUser) {
            $achievementCheck = new AchievementCheckEvent(Achievement::DOWNLOADER);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            $achievementCheck = new AchievementCheckEvent(Achievement::SUPER_DOWNLOADER);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            $achievementCheck = new AchievementCheckEvent(Achievement::ULTIMATE_DOWNLOADER);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
        }
    }
}
