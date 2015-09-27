<?php

namespace KI\ClubinfoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\ClubinfoBundle\Entity\Centrale;

class LoadCentraleFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $centrale = new Centrale();
        $centrale->setName('Clés USB');
        $centrale->setDescription('C\'est trotro cool, on va vous acheter pleins de clés USB pas cher du tout!');
        $centrale->setStartDate(1414242424);
        $centrale->setEndDate(1414242424);
        $centrale->setStatus('En cours');
        $this->addReference('centrale-cles-usb', $user);
        $manager->persist($centrale);

    }

    public function getOrder()
    {
        return 52;
    }
}
