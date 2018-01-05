<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ActorFixtures extends Fixture
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
}
