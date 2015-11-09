<?php

namespace KI\ClubinfoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\ClubinfoBundle\Entity\Commande;

class LoadCommandeFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $commande = new Commande();
        $commande->setQuantity(1);
        $commande->setCentrale($this->getReference('centrale-cles-usb'));
        $commande->setUser($this->getReference('user-taquet-c'));
        $commande->setTaken(true);
        $commande->setPaid(false);
        $manager->persist($commande);

        $commande = new Commande();
        $commande->setQuantity(42);
        $commande->setCentrale($this->getReference('centrale-cles-usb'));
        $commande->setUser($this->getReference('user-donat-bb'));
        $commande->setTaken(false);
        $commande->setPaid(false);
        $manager->persist($commande);

        $manager->flush();
    }

    public function getOrder()
    {
        return 53;
    }
}
