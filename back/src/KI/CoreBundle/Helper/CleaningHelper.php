<?php

namespace KI\CoreBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use KI\UserBundle\Repository\NotificationRepository;

// Valide les formulaires pour une entité et affiche la réponse à la demande
class CleaningHelper
{
    protected $manager;
    protected $notificationRepository;

    public function __construct(EntityManagerInterface $manager, NotificationRepository $notificationRepository)
    {
        $this->manager                = $manager;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Nettoie la base de données, procédure lancée par crontab
     */
    public function clean()
    {
        $this->cleanNotifications();
    }

    protected function cleanNotifications()
    {
        $notifications = $this->notificationRepository->findAll();

        foreach ($notifications as $notification) {
            if ($notification->getDate() < time() - 15*24*3600) {
                $this->manager->remove($notification);
            }
        }

        $this->manager->flush();
    }
}
