<?php

namespace App\DataFixtures;

use App\Entity\Tuto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TutoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tuto = new Tuto();
        $tuto->setName('Réglages Internet');
        $tuto->setText('Pour régler le proxy sous Windows, allez dans "Options Internet" (cf Panneau de configuration/barre de recherche du menu démarrer pour Windows Vista+), onglet "Connexions", bouton "Paramètres réseau". Il suffit de régler le proxy : (etu)proxy.enpc.fr -- port 3128.');
        $tuto->setDate(1414242424);
        $tuto->setIcon('globe');
        $manager->persist($tuto);

        $tuto = new Tuto();
        $tuto->setName('Filtrer vos mails');
        $tuto->setText('Sous Zimbra : aller dans l\'onglet "Préférences", et là vous avez un onglet "Filtres". Créer un filtre est très facile !');
        $tuto->setDate(1418325122);
        $manager->persist($tuto);

        $tuto = new Tuto();
        $tuto->setName('Création de club');
        $tuto->setText('Pour ceux ou celles qui auraient envie de créer un club, voici une page expliquant ce qu\'il faut faire !');
        $tuto->setDate(1412831521);
        $tuto->setIcon('users');
        $manager->persist($tuto);

        $manager->flush();
    }
}
