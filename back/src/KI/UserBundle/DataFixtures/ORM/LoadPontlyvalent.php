<?php

namespace KI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UserBundle\Entity\Pontlyvalent;


class LoadPontlyvalentFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($this->getReference('user-trancara'));
        $pontlyvalent->setAuthor($this->getReference('user-taquet-c'));
        $pontlyvalent->setText('Vieux Prez\' qui perd trop souvent');
        $pontlyvalent->setDate(1414242424);
        $manager->persist($pontlyvalent);

        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($this->getReference('user-taquet-c'));
        $pontlyvalent->setAuthor($this->getReference('user-muzardt'));
        $pontlyvalent->setText('Meilleure successeur possible <3');
        $pontlyvalent->setDate(1418325122);
        $manager->persist($pontlyvalent);

        $manager->flush();
    }

    public function getOrder()
    {
        return 16;
    }
}
