<?php

namespace KI\ClubinfoBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\ClubinfoBundle\Service\SlackService;
use KI\ClubinfoBundle\Entity\Fix;

class FixListener
{
    protected $slackService;

    public function __construct(SlackService $slackService)
    {
        $this->slackService = $slackService;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceOf Fix) {
            $this->slackService->post(
                $entity->getUser(),
                $entity->getFix() ? '#depannage' : '#upont-feedback',
                $entity->getProblem()
            );
        }
    }
}
