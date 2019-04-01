<?php

namespace App\Service;

use App\Entity\Achievement;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use DateTime;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CalendarService
{
    protected $icsProvider;
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
     * @param  User $user Le posesseur du calendrier
     * @param  array $events Les événements pour populer le calendrier
     * @return string
     */
    public function getCalendar(User $user, array $events, array $courses): string
    {
        // Titre et description
        $calendar = new Calendar('upont.enpc.fr');
        $calendar
            ->setName('Calendrier uPont')
            ->setDescription('Calendrier ICS des évènements uPont');

        // Positionnement des événements
        foreach ($events as $event) {
            $calendarEvent = new Event();
            $calendarEvent
                ->setDtStart($event->getStartDate())
                ->setDtEnd($event->getEndDate())
                ->setSummary($event->getName())
                ->setDescription($event->getText())
                ->setLocation($event->getPlace());

            $calendar->addComponent($calendarEvent);
        }

        foreach ($courses as $course) {
            $calendarEvent = new Event();
            $name = $course->getCourse()->getName();
            if ($course->getGroup() !== 0)
                $name .= " (Gr" . $course->getGroup() . ")";

            $calendarEvent
                ->setDtStart($this->toDateTime($course->getStartDate()))
                ->setDtEnd($this->toDateTime($course->getEndDate()))
                ->setSummary($name)
                ->setLocation($course->getLocation());

            $calendar->addComponent($calendarEvent);
        }

        $achievementCheck = new AchievementCheckEvent(Achievement::ICS_CALENDAR, $user);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        return $calendar->render();
    }
}
