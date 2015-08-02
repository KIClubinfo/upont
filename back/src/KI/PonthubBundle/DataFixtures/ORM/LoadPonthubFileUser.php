<?php

namespace KI\PonthubBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PonthubBundle\Entity\PonthubFileUser;

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
        $ponthubFileUser->setDate(time() - 3*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('movie-pumping-iron'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 4*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('game-age-of-empires-2'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 17*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('episode-pilot'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 20*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('other-windows-vista'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 455*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('music-shoot-to-thrill'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 456*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('music-shoot-to-thrill'));
        $ponthubFileUser->setUser($this->getReference('user-de-boisc'));
        $ponthubFileUser->setDate(time() - 456*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('music-giving-the-dog-a-bone'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 457*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('music-play-ball'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 458*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('music-enter-sandman'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 800*3600);
        $manager->persist($ponthubFileUser);

        $ponthubFileUser = new PonthubFileUser();
        $ponthubFileUser->setFile($this->getReference('software-windows'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 850*3600);
        $manager->persist($ponthubFileUser);

        $manager->flush();
    }

    public function getOrder()
    {
        return 42;
    }
}
