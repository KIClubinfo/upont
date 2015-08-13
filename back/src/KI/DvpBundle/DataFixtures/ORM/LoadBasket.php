<?php

namespace KI\DvpBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\DvpBundle\Entity\Basket;

class LoadBasketFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $basket = new Basket();
        $basket->setName('Panier moyen');
        $basket->setContent('Des fruits');
        $basket->setPrice(10);
        $manager->persist($basket);

        $basket = new Basket();
        $basket->setName('Gros panier');
        $basket->setContent('Un peu de tout');
        $basket->setPrice(15.5);
        $manager->persist($basket);

        $manager->flush();
    }

    public function getOrder()
    {
        return 70;
    }
}
