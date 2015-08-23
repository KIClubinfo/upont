<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KI\CoreBundle\Service\CurlService;
use KI\UserBundle\Entity\Device;
use KI\UserBundle\Entity\Notification;

class NotificationListener
{
    protected $gcmKey;
    protected $curlService;

    public function __construct(CurlService $curlService, $gcmKey)
    {
        $this->curlService = $curlService;
        $this->gcmKey = $gcmKey;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $notification = $args->getEntity();

        if (!$notification instanceof Notification)
            return;

        $manager = $args->getEntityManager();
        $repo = $manager->getRepository('KIUserBundle:Device');
        $devices = $repo->findAll();
        $sendToAndroid = $sendToIOS = $sendToWP = array();

        // Si le mode d'envoi est direct, on envoie aux utilisateurs qui ont
        // enregistré un ou plusieurs Devices
        if ($notification->getMode() == 'to') {
            foreach ($notification->getRecipient() as $user) {
                // Si l'utilisateur a indiqué ne pas vouloir recevoir la notification
                if (!$user->getPreferences()[$notification->getReason()])
                    continue;

                foreach ($user->getDevices() as $device) {
                    if ($device->getType() == 'Android')
                        $sendToAndroid[] = $device;
                    else if ($device->getType() == 'iOS')
                        $sendToIOS[] = $device;
                    else if ($device->getType() == 'WP')
                        $sendToWP[] = $device;
                }
            }
        }

        // Si on est en mode exclusion, on parcourt les Devices enregistrés
        // et on envoie à ceux qui ne sont pas dans la liste d'exclusion
        if ($notification->getMode() == 'exclude') {
            $list = $notification->getRecipient();
            foreach ($devices as $device) {
                if (!$list->contains($device->getOwner())) {
                    // Si l'utilisateur a indiqué ne pas vouloir recevoir la notification
                    if (!$device->getOwner()->getPreferences()[$notification->getReason()])
                        continue;

                    if ($device->getType() == 'Android')
                        $sendToAndroid[] = $device->getDevice();
                    else if ($device->getType() == 'iOS')
                        $sendToIOS[] = $device->getDevice();
                    else if ($device->getType() == 'WP')
                        $sendToWP[] = $device->getDevice();
                }
            }
        }

        // Maintenant qu'on a la liste des appareils auxquels envoyer, on envoie
        $this->pushAndroid($notification, $sendToAndroid);
        $this->pushIOS($notification, $sendToIOS);

        // Microsoft étant d'immense débiles, on ne peut pas envoyer à plein de
        // destinataires en une fois. On est donc obligé de faire autant de
        // requêtes vers l'extérieur que de destinataires.
        // À corriger quand ils feront preuve d'intelligence chez Microsoft.
        foreach ($sendToWP as $device) {
            //pushWP
        }
    }

    public function pushAndroid(Notification $notification, array $to)
    {
        $message = array(
            'title'     => $notification->getTitle(),
            'message'   => $notification->getMessage(),
            'vibrate'   => 1,
            'sound'     => 1,
        );

        $fields = array(
            'registration_ids'     => $to,
            'data'                => $message
        );

        $headers = array(
            'Authorization: key='.$this->gcmKey,
            'Content-Type: application/json'
        );

        $this->curlService->curl('https://android.googleapis.com/gcm/send', null, array(
            CURLOPT_HEADER     => true,
            CURLOPT_POST       => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($fields)
        ));
    }

    public function pushIOS(Notification $notification, array $to)
    {

    }

    public function pushWP(Notification $notification, Device $device)
    {
        $message = '<?xml version="1.0" encoding="utf-8"?>'.
                    '<wp:Notification xmlns:wp="WPNotification">'.
                    '<wp:Toast>'.
                    '<wp:Text1>uPont</wp:Text1>'.
                    '<wp:Text2>'.htmlspecialchars($notification->getTitle()).'</wp:Text2>'.
                    '</wp:Toast>'.
                    '</wp:Notification>';

        $headers = array(
            'Content-Type: text/xml',
            'Accept: application/*',
            'X-NotificationClass: 0',
            'X-WindowsPhone-Target:toast'
        );

        $this->curlService->curl($device->getDevice(), null, array(
            CURLOPT_HEADER     => true,
            CURLOPT_POST       => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $message
        ));
    }
}
