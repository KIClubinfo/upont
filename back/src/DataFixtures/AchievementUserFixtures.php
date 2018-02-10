<?php

namespace App\DataFixtures;

use App\Entity\AchievementUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class AchievementUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $achievementUser = new AchievementUser();
        $achievementUser->setAchievement($this->getReference('achievement-170'));
        $achievementUser->setUser($this->getReference('user-taquet-c'));
        $achievementUser->setDate(time());
        $manager->persist($achievementUser);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            AchievementFixtures::class,
        ];
    }
}
