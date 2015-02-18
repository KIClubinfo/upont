<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use KI\UpontBundle\Entity\Users\User;


//Service permettant de gérer les calendrier
class KICalendar extends ContainerAware
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    private function toDateTime($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    //Retourne un calendrier au format ICS
    public function getCalendar(User $user)
    {
        $provider = $this->container->get('bomo_ical.ics_provider');

        //On se positionne à Paris
        $tz = $provider->createTimezone();
        $tz->setTzid('Europe/Paris')->setProperty('X-LIC-LOCATION', $tz->getTzid());

        //Titre et description
        $cal = $provider->createCalendar($tz);
        $cal->setName('Calendrier Youpont')
            ->setDescription('Calendrier ICS des évènements YouPont');

        $repoClub = $this->em->getRepository('KIUpontBundle:Users\Club');
        $clubs = $repoClub->findAll();
        $clubsNotFollowed = $user->getClubsNotFollowed();
        $followedClubs = array();
        foreach($clubs as $club) {
            if (!$clubsNotFollowed->contains($club))
                $followedClubs[] = $club;
        }

        // On va chercher les événements voulus
        $repoEvent = $this->em->getRepository('KIUpontBundle:Publications\Event');
        $followedEvents = $repoEvent->findBy(array('authorClub'=> $followedClubs));
        $persoEvents = $repoEvent->findBy(array('authorUser' => $user, 'authorClub' => null));
        $events = array_merge($followedEvents, $persoEvents);

        // Tri et élimination des données
        $dates = array();
        $listEvents = array();
        $today = mktime(0, 0, 0);
        foreach ($events as $key => $event) {
            if ($event->getStartDate() > $today) {
                $listEvents[$key] = $event;
                $dates[$key] = $event->getStartDate();
            }
        }
        array_multisort($dates, SORT_DESC, $listEvents);

        foreach ($listEvents as $eventDb) {
            $datetime = new \Datetime();
            $event = $cal->newEvent();
            $event
                ->setStartDate($this->toDateTime($eventDb->getStartDate()))
                ->setEndDate($this->toDateTime($eventDb->getEndDate()))
                ->setName($eventDb->getName())
                ->setDescription($eventDb->getTextLong())
                ->setAttendee('yael.mith@eleves.enpc.fr')
                ->setAttendee('Yaya Mith');
        }

        /*$alarm = $event->newAlarm();
        $alarm
            ->setAction('display')
            ->setDescription($event->getProperty('description'))
            ->setTrigger('-PT2H') //See Dateinterval string format
        ;*/

        // All Day event
        /*$event = $cal->newEvent();
        $event
            ->setIsAllDayEvent()
            ->setStartDate($datetime)
            ->setEndDate($datetime->modify('+10 days'))
            ->setName('All day event')
            ->setDescription('All day visualisation')
        ;*/
        return $cal->returnCalendar();
    }
}
