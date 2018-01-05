<?php

namespace App\DataFixtures;

use App\Entity\Pontlyvalent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class PontlyvalentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($this->getReference('user-vessairc'));
        $pontlyvalent->setAuthor($this->getReference('user-taquet-c'));
        $pontlyvalent->setText('Nécromancien ultra doué :o');
        $pontlyvalent->setDate(1414242424);
        $manager->persist($pontlyvalent);

        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($this->getReference('user-taquet-c'));
        $pontlyvalent->setAuthor($this->getReference('user-peluchom'));
        $pontlyvalent->setText('Meilleure présidente du KI <3');
        $pontlyvalent->setDate(1418325122);
        $manager->persist($pontlyvalent);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
