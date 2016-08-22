<?php

namespace KI\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\Notification;
use KI\UserBundle\Service\GcmService;

class NotifyService
{
    protected $gcmService;
    protected $manager;
    protected $deviceRepository;
    protected $userRepository;

    public function __construct(GcmService $gcmService, EntityManager $manager, EntityRepository $deviceRepository, EntityRepository $userRepository)
    {
        $this->gcmService       = $gcmService;
        $this->manager          = $manager;
        $this->deviceRepository = $deviceRepository;
        $this->userRepository   = $userRepository;
    }

    // Persiste des objets notification qui seront retrievables
    public function notify($reason, $title, $message, $mode = 'to', $recipient = [], $resource = '')
    {
        $notification = new Notification($reason, $title, $message, $mode, $resource);

        if ($mode == 'to') {
            if (is_array($recipient)) {
                foreach ($recipient as $user) {
                    $notification->addRecipient($user);
                }
            } else {
                $notification->addRecipient($recipient);
            }
        } else if ($mode == 'exclude') {
            $users = $this->userRepository->findAll();

            foreach ($users as $user) {
                if (!in_array($user, $recipient)) {
                    $notification->addRecipient($user);
                }
            }
        }

        // On stocke la notif pour usage ultérieur
        $this->manager->persist($notification);
        $this->manager->flush();

        // On la pousse vers les téléphones
        $this->pushToCloud($notification);
    }

    // Envoie une notif vers les divers services de messagerie
    private function pushToCloud(Notification $notification)
    {
        $devices = $this->deviceRepository->findAll();
        $sendToAndroid = [];

        // Si le mode d'envoi est direct, on envoie aux utilisateurs qui ont
        // enregistré un ou plusieurs Devices
        if ($notification->getMode() == 'to') {
            foreach ($notification->getRecipient() as $user) {
                // Si l'utilisateur a indiqué ne pas vouloir recevoir la notification
                if (!$user->getPreferences()[$notification->getReason()]) {
                    continue;
                }

                foreach ($user->getDevices() as $device) {
                    switch ($device->getType()) {
                    case 'Android':
                        $sendToAndroid[] = $device->getDevice();
                        break;
                    }
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
                    if (!$device->getOwner()->getPreferences()[$notification->getReason()]) {
                        continue;
                    }

                    switch ($device->getType()) {
                    case 'Android':
                        $sendToAndroid[] = $device->getDevice();
                        break;
                    }
                }
            }
        }
        $this->gcmService->push($notification, $sendToAndroid);
    }
}
