<?php

namespace KI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UserBundle\Entity\Family;


class LoadClubFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $club = new Family();
        $club->setName('La DDASS');
        $club->setFullName('La DDASS');
        $club->setImage($this->getReference('image-family-la-ddass'));
        $club->setPresentation('La meilleure famille des Ponts ! <3');
        $manager->persist($club);
        $this->addReference('family-la-ddass', $family);

        $manager->flush();
    }
}
