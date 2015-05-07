<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Admissible;


class LoadAdmissibleFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $admissible = new Admissible();
        $admissible->setFirstName('Kévin');
        $admissible->setLastName('Toucourt');
        $admissible->setDate(1235389);
        $admissible->setScei('4242');
        $admissible->setSerie(2);
        $admissible->setContact('kevin.toucourt@yahoo.fr ; 0612345678');
        $admissible->setRoom('Simple');
        $manager->persist($admissible);

        $admissible = new Admissible();
        $admissible->setFirstName('Cécile');
        $admissible->setLastName('Taquet Gaspérini');
        $admissible->setDate(1403205420);
        $admissible->setScei('21132');
        $admissible->setSerie(4);
        $admissible->setContact('cecile.taquet-gasperini@eleves.enpc.fr ; 0637008206');
        $admissible->setRoom('Hôtel');
        $admissible->setDetails('Ceci est un placeholder.');
        $manager->persist($admissible);

        $manager->flush();
    }

    public function getOrder()
    {
        return 29;
    }
}
