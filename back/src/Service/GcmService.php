<?php

namespace App\Service;

use App\Service\CurlService;
use App\Entity\Notification;

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
        $message = [
            'title'   => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'vibrate' => 1,
            'sound'   => 1
        ];

        $fields = [
            'registration_ids' => $to,
            'data'             => $message
        ];

        $headers = [
            'Authorization: key='.$this->gcmKey,
            'Content-Type: application/json'
        ];

        $this->curlService->curl('https://android.googleapis.com/gcm/send', json_encode($fields), [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER     => true
        ]);
    }
}
