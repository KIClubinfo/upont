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
    public function getCalendar(User $user, array $events)
    {
        $provider = $this->container->get('bomo_ical.ics_provider');

        //On se positionne à Paris
        $tz = $provider->createTimezone();
        $tz->setTzid('Europe/Paris')->setProperty('X-LIC-LOCATION', $tz->getTzid());

        //Titre et description
        $cal = $provider->createCalendar($tz);
        $cal->setName('Calendrier uPont')
            ->setDescription('Calendrier ICS des évènements uPont');

        foreach ($events as $eventDb) {
            $event = $cal->newEvent();
            $event
                ->setStartDate($this->toDateTime($eventDb->getStartDate()))
                ->setEndDate($this->toDateTime($eventDb->getEndDate()))
                ->setName($eventDb->getName())
                ->setLocation($eventDb->getPlace());
        }

        return $cal->returnCalendar();
    }
}
