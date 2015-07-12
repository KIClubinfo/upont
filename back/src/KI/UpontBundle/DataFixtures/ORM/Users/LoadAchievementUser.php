<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\AchievementUser;


class LoadAchievementUserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $achievementUser = new AchievementUser();
        $achievementUser->setAchievement($this->getReference('achievement-17'));
        $achievementUser->setUser($this->getReference('user-taquet-c'));
        $achievementUser->setDate(time());
        $manager->persist($achievementUser);

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}
