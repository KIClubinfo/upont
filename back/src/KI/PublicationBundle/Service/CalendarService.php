<?php

namespace KI\PublicationBundle\Service;

use BOMO\IcalBundle\Provider\IcsProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;

class CalendarService
{
    protected $icsProvider;
    protected $dispatcher;

    public function __construct(IcsProvider $icsProvider, EventDispatcherInterface $dispatcher)
    {
        $this->icsProvider = $icsProvider;
        $this->dispatcher  = $dispatcher;
    }

    /**
     * Convertit un timestamp à un Datetime PHP
     * @param  int $timestamp
     * @return DateTime
     */
    private function toDateTime($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    /**
     * Retourne un calendrier au format ICS
     * @param  User   $user   Le posesseur du calendrier
     * @param  array  $events Les événements pour populer le calendrier
     * @return object
     */
    public function getCalendar(User $user, array $events)
    {
        // On se positionne à Paris
        $tz = $this->icsProvider->createTimezone();
        $tz->setTzid('Europe/Paris')->setProperty('X-LIC-LOCATION', $tz->getTzid());

        // Titre et description
        $cal = $this->icsProvider->createCalendar($tz);
        $cal
            ->setName('Calendrier uPont')
            ->setDescription('Calendrier ICS des évènements uPont')
        ;

        // Positionnement des événements
        foreach ($events as $eventDb) {
            $event = $cal->newEvent();
            $event
                ->setStartDate($this->toDateTime($eventDb->getStartDate()))
                ->setEndDate($this->toDateTime($eventDb->getEndDate()))
                ->setName($eventDb->getName())
                ->setDescription($eventDb->getText())
                ->setLocation($eventDb->getPlace())
            ;
        }

        $achievementCheck = new AchievementCheckEvent(Achievement::ICS_CALENDAR, $user);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        return $cal->returnCalendar();
    }
}
