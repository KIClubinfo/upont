<?php

namespace KI\ClubinfoBundle\Service;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\CoreBundle\Service\CurlService;
use KI\ClubinfoBundle\Entity\Fix;
use KI\UserBundle\Entity\User;

class SlackService
{
    protected $curlService;
    protected $slackHook;
    protected $environment;

    public function __construct(CurlService $curlService, $slackHook, $environment)
    {
        $this->curlService  = $curlService;
        $this->slackHook    = $slackHook;
        $this->$environment = $environment;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceOf Fix && !in_array($this->environment, array('dev', 'test'))) {
            $this->post(
                $entity->getUser(),
                $entity->getFix() ? '#depannage' : '#upont-feedback',
                '"'.$entity->getProblem().'"'
            );
        }
    }

    // Téléchargement d'une ressource externe
    public function post(User $user, $channel, $text)
    {
        $payload = array(
            'channel' => $channel,
            'username' => $user->getFirstname().' '.$user->getLastname(),
            'icon_url' => 'https://upont.enpc.fr/api/'.$user->getImage()->getWebPath(),
            'text' => '"'.$text.'"'
        );

        return $this->curlService->curl($this->slackHook, json_encode($payload));
    }
}
