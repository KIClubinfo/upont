<?php

namespace App\DataFixtures;

use App\Entity\Software;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\PonthubFileUser;

class PonthubFileUserFixtures extends Fixture implements DependentFixtureInterface
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
        $ponthubFileUser->setFile($this->getReference('software-windows'));
        $ponthubFileUser->setUser($this->getReference('user-trancara'));
        $ponthubFileUser->setDate(time() - 850*3600);
        $manager->persist($ponthubFileUser);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            GameFixtures::class,
            OtherFixtures::class,
            SoftwareFixtures::class,
            EpisodeFixtures::class,
            MovieFixtures::class
        ];
    }
}
