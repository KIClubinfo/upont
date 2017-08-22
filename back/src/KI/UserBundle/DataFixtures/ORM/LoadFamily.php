<?php

namespace KI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UserBundle\Entity\Family;


class LoadFamilyFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $family = new Family();
        $family->setName('La DDASS');
        $family->setFullName('La DDASS');
        $family->setImage($this->getReference('image-family-la-ddass'));
        $family->setPresentation('La meilleure famille des Ponts ! <3');
        $manager->persist($family);
        $this->addReference('family-la-ddass', $family);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
