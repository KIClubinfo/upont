<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Achievement;


class LoadAchievementFixture extends AbstractFixture implements OrderedFixtureInterface
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

    public function getOrder()
    {
        return 10;
    }
}
