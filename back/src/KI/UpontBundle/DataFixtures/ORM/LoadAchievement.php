<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Achievement;


class LoadAchievementFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Nombre total d'achievements à actualiser à chaque fois
        $nb = 51;
        
        for($i = 0; $i < $nb; $i++) {
            $achievement = new Achievement($i);
            $manager->persist($achievement);
            $this->addReference('achievement-' . $i, $achievement);
        }
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 10;
    }
}
