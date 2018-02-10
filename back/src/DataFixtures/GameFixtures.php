<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class GameFixtures extends Fixture implements DependentFixtureInterface
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

    public function getDependencies()
    {
        return [
            ImageFixtures::class,
            TagFixtures::class
        ];
    }
}
