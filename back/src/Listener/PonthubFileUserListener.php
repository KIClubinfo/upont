<?php

namespace App\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\PonthubFileUser;
use App\Entity\Achievement;
use App\Event\AchievementCheckEvent;
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
            $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::DOWNLOADER));
            $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::SUPER_DOWNLOADER));
            $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::ULTIMATE_DOWNLOADER));
        }
    }
}
