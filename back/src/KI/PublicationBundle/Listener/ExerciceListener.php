<?php

namespace KI\PublicationBundle\Listener;

use Doctrine\ORM\EntityRepository;
use KI\PublicationBundle\Entity\Exercice;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Service\NotifyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExerciceListener
{
    protected $notifyService;
    protected $dispatcher;
    protected $courseUserRepository;

    public function __construct(NotifyService $notifyService,
                                EventDispatcherInterface $dispatcher,
                                EntityRepository $courseUserRepository)
    {
        $this->notifyService        = $notifyService;
        $this->dispatcher           = $dispatcher;
        $this->courseUserRepository = $courseUserRepository;
    }

    public function postPersist(Exercice $exercice)
    {
        $achievementCheck = new AchievementCheckEvent(Achievement::POOKIE);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        // On crée une notification
        $course = $exercice->getCourse();
        $courseUsers = $this->courseUserRepository->findBy(['course' => $course]);
        $users = [];

        foreach ($courseUsers as $courseUser) {
            $users[] = $courseUser->getUser();
        }

        $this->notifyService->notify(
            'notif_followed_annal',
            $exercice->getName(),
            'Une annale pour le cours '.$course->getName().' est maintenant disponible',
            'to',
            $users
        );
    }
}
