<?php

namespace KI\DvpBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\DvpBundle\Entity\BasketDate;

class LoadBasketDateFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();

        $date->modify('next thursday');
        $basketDate = new BasketDate();
        $basketDate->setDateRetrieve($date);
        $manager->persist($basketDate);

        $manager->flush();

        $date->modify('next thursday');
        $basketDate = new BasketDate();
        $basketDate->setDateRetrieve($date);
        $manager->persist($basketDate);

        $manager->flush();
    }

    public function getOrder()
    {
        return 71;
    }
}
