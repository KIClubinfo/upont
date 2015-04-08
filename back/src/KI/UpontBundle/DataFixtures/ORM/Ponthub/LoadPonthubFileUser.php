<?php

namespace KI\UpontBundle\DataFixtures\ORM\Ponthub;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Ponthub\PonthubFileUser;

class LoadPonthubFileUserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('movie-pumping-iron'));
        $ponthubFileUser->setUser($this->getReference('user-taquet-c'));
        $ponthubFileUser->setDate(time() - 3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('movie-pumping-iron'));
        $ponthubFileUser->setUser($this->getReference('user-de-boisc'));
        $ponthubFileUser->setDate(time() - 3600);
        $manager->persist($ponthubFileUser);

        $manager->flush();
    }

    public function getOrder()
    {
        return 42;
    }
}
