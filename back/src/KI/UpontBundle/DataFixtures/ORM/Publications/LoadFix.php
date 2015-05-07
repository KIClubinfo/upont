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
        $fix->setProblem('Coucou le KI, j\'ai un gros problème : quand je suis dans ma chambre à Meunier, je n\'ai pas accès à Internet, alors qu\' à l\'école ça marche ! :((');
        $fix->setAnswer('Faut que tu règles le proxy correctement. Va voir le tuto : upont.enpc.fr/tutos/proxy');
        $fix->setDate(1414242424);
        $fix->setSolved(1414242425);
        $fix->setStatus('Résolu');
        $fix->setCategory('Réseau');
        $manager->persist($fix);

        $fix = new Fix();
        $fix->setName('Ordinateur en rade');
        $fix->setProblem('Je ne comprends pas, je reçois énormément de pop-ups publicitaires, et mon ordi est très très lent... Venez vite dans ma chambre pour m\'aider svp !');
        $fix->setAnswer('Ok on arrive.');
        $fix->setDate(12345234);
        $fix->setStatus('En attente');
        $fix->setCategory('Problème logiciel');
        $manager->persist($fix);

        $manager->flush();
    }

    public function getOrder()
    {
        return 27;
    }
}
