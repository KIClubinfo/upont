<?php

namespace App\Service;

use BOMO\IcalBundle\Provider\IcsProvider;
use App\Entity\Achievement;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use Carbon\Carbon;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \DateTime;

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
    public function getCalendar(User $user, array $events, array $courses = [])
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
                ->setStartDate($eventDb->getStartDate())
                ->setEndDate($eventDb->getEndDate())
                ->setName($eventDb->getName())
                ->setDescription($eventDb->getText())
                ->setLocation($eventDb->getPlace())
            ;
        }

        foreach ($courses as $course){
            $event = $cal->newEvent();
            $name = $course->getCourse()->getName();
            if ($course->getGroup() !== 0)
                $name .= " (Gr".$course->getGroup().")";

            $event
                ->setStartDate($this->toDateTime($course->getStartDate()))
                ->setEndDate($this->toDateTime($course->getEndDate()))
                ->setName($name)
                ->setLocation($course->getLocation())
            ;
        }

        $achievementCheck = new AchievementCheckEvent(Achievement::ICS_CALENDAR, $user);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        return $cal->returnCalendar();
    }
}
