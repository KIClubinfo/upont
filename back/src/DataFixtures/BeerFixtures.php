<?php

namespace App\DataFixtures;

use App\Entity\Beer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class BeerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $beer = new Beer();
        $beer->setName('Kro');
        $beer->setPrice(1);
        $beer->setAlcohol(5);
        $beer->setVolume(0.5);
        $this->addReference('beer-kro', $beer);
        $manager->persist($beer);

        $beer = new Beer();
        $beer->setName('Leffe');
        $beer->setPrice(1.2);
        $beer->setAlcohol(8);
        $beer->setVolume(0.33);
        $this->addReference('beer-leffe', $beer);
        $manager->persist($beer);

        $manager->flush();
    }
}
