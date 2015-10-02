<?php

namespace KI\UserBundle\Service;

use KI\CoreBundle\Service\CurlService;
use KI\UserBundle\Entity\Notification;

class GcmService
{
    protected $curlService;
    protected $gcmKey;

    public function __construct(CurlService $curlService, $gcmKey)
    {
        $this->curlService = $curlService;
        $this->gcmKey      = $gcmKey;
    }

    public function push(Notification $notification, array $to)
    {
        $message = array(
            'title'   => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'vibrate' => 1,
            'sound'   => 1
        );

        $fields = array(
            'registration_ids' => $to,
            'data'             => $message
        );

        $headers = array(
            'Authorization: key='.$this->gcmKey,
            'Content-Type: application/json'
        );

        $this->curlService->curl('https://android.googleapis.com/gcm/send', json_encode($fields), array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER     => true
        ));
    }
}
