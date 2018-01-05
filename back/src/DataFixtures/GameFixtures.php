<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Game;

class GameFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $game = new Game();
        $game->setSize(487864000);
        $game->setPath('/root/web/jeux/Age of Empires 2 - Conquerors - Forgotten Empires.rar');
        $game->setName('Age of Empires 2');
        $game->setYear(1999);
        $game->setDescription('Un jeu à apporter absolument aux LANs ! Contient l\'extension Forgotten Empires, version HD !');
        $game->setTags([$this->getReference('tag-poseeey')]);
        $game->setStatus('OK');
        $game->setImage($this->getReference('image-game-age-of-empires-2'));
        $game->setOs('Windows');
        $manager->persist($game);
        $this->addReference('game-age-of-empires-2', $game);

        $manager->flush();
    }

    public function getOrder()
    {
        return 27;
    }
}
