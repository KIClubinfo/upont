<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class AchievementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Nombre total d'achievements à actualiser à chaque fois
        foreach (Achievement::getConstants() as $constant) {
            $achievement = new Achievement($constant);
            $manager->persist($achievement);
            $this->addReference('achievement-' . $constant, $achievement);
        }

        $manager->flush();
    }
}
