<?php

namespace KI\PublicationBundle\Service;

use BOMO\IcalBundle\Provider\IcsProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;

//Service permettant de gérer les calendrier
class CalendarService
{
    protected $icsProvider;
    protected $dispatcher;
    protected $manager;

    public function __construct(IcsProvider $icsProvider, EventDispatcherInterface $dispatcher, EntityManager $manager)
    {
        $this->icsProvider = $icsProvider;
        $this->dispatcher  = $dispatcher;
        $this->manager     = $manager;
    }

    private function toDateTime($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    // Retourne un calendrier au format ICS
    public function getCalendar(User $user, array $events)
    {
        //On se positionne à Paris
        $tz = $this->icsProvider->createTimezone();
        $tz->setTzid('Europe/Paris')->setProperty('X-LIC-LOCATION', $tz->getTzid());

        //Titre et description
        $cal = $this->icsProvider->createCalendar($tz);
        $cal->setName('Calendrier uPont')
            ->setDescription('Calendrier ICS des évènements uPont');

        foreach ($events as $eventDb) {
            $event = $cal->newEvent();
            $event
                ->setStartDate($this->toDateTime($eventDb->getStartDate()))
                ->setEndDate($this->toDateTime($eventDb->getEndDate()))
                ->setName($eventDb->getName())
                ->setDescription($eventDb->getText())
                ->setLocation($eventDb->getPlace());
        }

        $achievementCheck = new AchievementCheckEvent(Achievement::ICS_CALENDAR, $user);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        return $cal->returnCalendar();
    }
}
