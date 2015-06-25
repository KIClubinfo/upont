<?php

namespace KI\UpontBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\Fix;

class LoadFixFixture extends AbstractFixture implements OrderedFixtureInterface
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

        $manager->flush();
    }

    public function getOrder()
    {
        return 27;
    }
}
