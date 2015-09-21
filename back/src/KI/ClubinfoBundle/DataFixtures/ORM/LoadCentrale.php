<?php

namespace KI\ClubinfoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\ClubinfoBundle\Entity\Centrale;

class LoadFixFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fix = new Centrale();
        $fix->setProduct('Clés USB');
        $fix->setDescription('C\'est trotro cool, on va vous acheter pleins de clés USB pas cher du tout!');
        $fix->setStartDate(1414242424);
        $fix->setEndDate(1414242424);
        $fix->setStatus('En cours');
        $manager->persist($fix);

    }

    public function getOrder()
    {
        return 52;
    }
}
