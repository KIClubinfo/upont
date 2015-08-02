<?php

namespace KI\ClubinfoBundle\Service;

use KI\CoreBundle\Service\CurlService;
use KI\UserBundle\Entity\User;

class SlackService
{
    protected $curlService;
    protected $slackHook;
    protected $environment;
    protected $baseUrl;

    public function __construct(CurlService $curlService, $slackHook, $environment, $baseUrl)
    {
        $this->curlService  = $curlService;
        $this->slackHook    = $slackHook;
        $this->environment  = $environment;
        $this->baseUrl      = $baseUrl;
    }

    // Téléchargement d'une ressource externe
    public function post(User $user, $channel, $text)
    {
        if (in_array($this->environment, array('dev', 'test'))) {
            return;
        }

        $payload = array(
            'channel'  => $channel,
            'username' => $user->getFirstname().' '.$user->getLastname(),
            'icon_url' => $this->baseUrl.$user->getImage()->getWebPath(),
            'text'     => $text
        );

        return $this->curlService->curl($this->slackHook, json_encode($payload));
    }
}
