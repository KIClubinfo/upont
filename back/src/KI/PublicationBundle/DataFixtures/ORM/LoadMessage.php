<?php

namespace KI\PublicationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PublicationBundle\Entity\Message;

class LoadMessageFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Messages persos
        $newsitem = new Message();
        $newsitem->setName('message');
        $newsitem->setText('Venez tous jouer à AgeOf !');
        $newsitem->setDate(time() - 3600);
        $newsitem->setAuthorUser($this->getReference('user-trancara'));
        $newsitem->setImage($this->getReference('image-game-age-of-empires-2'));
        $manager->persist($newsitem);

        $newsitem = new Message();
        $newsitem->setName('message');
        $newsitem->setText('[Le rêve de Jeanine]<br>Est ce que vous voyez la pluie tomber sur notre calme Champs sur Marne? Vous êtes vous seulement posés la question, ne serait-ce qu\'une fois, de ce que toute cette eau devenait? Cette nonchalance de votre part est permise seulement par la puissance de nouveaux radars qui mesurent précisément la minute et la rue où va se déverser le prochain orage sur les villes d\'Ile de France.');
        $newsitem->setDate(time() - 42*3600);
        $newsitem->setAuthorUser($this->getReference('user-dziris'));
        $manager->persist($newsitem);

        $newsitem = new Message();
        $newsitem->setName('Le béton c\'est bon.');
        $newsitem->setText('L\'acier aussi, c\'est complètement METAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAL.');
        $newsitem->setDate(time() - 21*3600);
        $newsitem->setAuthorUser($this->getReference('user-gcc'));
        $newsitem->setLikes(array($this->getReference('user-taquet-c')));
        $newsitem->setDislikes(array($this->getReference('user-trancara'), $this->getReference('user-dziris')));
        $newsitem->addComment($this->getReference('comment-rage'));
        $newsitem->addComment($this->getReference('comment-arret'));
        $manager->persist($newsitem);

        $manager->flush();
    }

    public function getOrder()
    {
        return 42;
    }
}
