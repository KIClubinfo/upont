<?php

namespace App\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Fix;
use App\Service\SlackService;
use App\Entity\Achievement;
use App\Event\AchievementCheckEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FixListener
{
    protected $slackService;
    protected $dispatcher;

    public function __construct(SlackService $slackService, EventDispatcherInterface $dispatcher)
    {
        $this->slackService = $slackService;
        $this->dispatcher   = $dispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceOf Fix) {
            // On poste sur Slack
            $this->slackService->post(
                $entity->getUser(),
                $entity->getFix() ? '#depannage' : '#upont-feedback',
                $entity->getProblem()
            );

            // On regarde les achievements
            if ($entity->getFix()) {
                $achievementCheck = new AchievementCheckEvent(Achievement::BUG_CONTACT);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            } else {
                $achievementCheck = new AchievementCheckEvent(Achievement::BUG_REPORT);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            }
        }
    }
}
