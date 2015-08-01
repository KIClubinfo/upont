<?php

namespace KI\PonthubBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PonthubBundle\Entity\Actor;

class LoadActorFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $actor = new Actor();
        $actor->setName('Bruce Willis');
        $manager->persist($actor);
        $this->addReference('actor-bruce', $actor);

        $actor = new Actor();
        $actor->setName('Arnold Schwarzenegger');
        $manager->persist($actor);
        $this->addReference('actor-arnold', $actor);

        $actor = new Actor();
        $actor->setName('Brad Pitt');
        $manager->persist($actor);
        $this->addReference('actor-brad', $actor);

        $actor = new Actor();
        $actor->setName('Angelina Jolie');
        $manager->persist($actor);
        $this->addReference('actor-angelina', $actor);

        $actor = new Actor();
        $actor->setName('Monica Belluci');
        $manager->persist($actor);
        $this->addReference('actor-monica', $actor);

        $manager->flush();
    }

    public function getOrder()
    {
        return 32;
    }
}
