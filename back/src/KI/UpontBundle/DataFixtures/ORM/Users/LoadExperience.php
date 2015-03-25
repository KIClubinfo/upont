<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Experience;


class LoadExperienceFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $experience = new Experience();
        $experience->setName('Form finding of funicular shapes');
        $experience->setCategory('Stage scientifique');
        $experience->setStartDate(35446);
        $experience->setEndDate(75323);
        $experience->setDescription('Stage très intéressant');
        $experience->setCountry('Espagne');
        $experience->setCity('Madrid');
        $experience->setCompany('Universidad Polytecnica de Madrid');
        $experience->setLatitude(58.32);
        $experience->setLongitude(42.32);
        $experience->setUser($this->getReference('user-trancara'));
        $manager->persist($experience);

        $manager->flush();
    }

    public function getOrder()
    {
        return 24;
    }
}
