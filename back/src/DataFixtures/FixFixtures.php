<?php

namespace App\DataFixtures;

use App\Entity\Fix;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class FixFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fix = new Fix();
        $fix->setName('Mon Internet ne marche pas :(');
        $fix->setProblem('Coucou le KI, j\'ai un gros problème : quand je suis dans ma chambre à Meunier, je n\'ai pas accès à Internet, alors qu\' à l\'école ça marche ! :(( Est-ce que vous pouvez m\'aider ? J\'ai bien branché internet pourtant, je suis pas débile hein, mais ça veut pas marcher…');
        $fix->setDate(1414242424);
        $fix->setUser($this->getReference('user-trancara'));
        $fix->setSolved(1414242425);
        $fix->setStatus('Résolu');
        $fix->setFix(true);
        $manager->persist($fix);

        $fix = new Fix();
        $fix->setName('Bug');
        $fix->setProblem('uPont ca bug');
        $fix->setDate(12345234);
        $fix->setUser($this->getReference('user-donat-bb'));
        $fix->setStatus('En attente');
        $fix->setFix(false);
        $manager->persist($fix);

        $fix = new Fix();
        $fix->setName('Bug');
        $fix->setProblem('uPont ca bug');
        $fix->setDate(12345234);
        $fix->setUser($this->getReference('user-donat-bb'));
        $fix->setStatus('En attente');
        $fix->setFix(false);
        $manager->persist($fix);

        $fix = new Fix();
        $fix->setName('Bug');
        $fix->setProblem('uPont ca bug');
        $fix->setDate(12345234);
        $fix->setUser($this->getReference('user-donat-bb'));
        $fix->setStatus('En attente');
        $fix->setFix(false);
        $manager->persist($fix);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
