<?php

namespace KI\FoyerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\FoyerBundle\Entity\Beer;


class LoadBeerFixture extends AbstractFixture implements OrderedFixtureInterface
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

    public function getOrder()
    {
        return 61;
    }
}
