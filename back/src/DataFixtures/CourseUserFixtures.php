<?php

namespace App\DataFixtures;

use App\Entity\CourseUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class CourseUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-shark'));
        $courseUser->setUser($this->getReference('user-taquet-c'));
        $courseUser->setGroup(0);
        $manager->persist($courseUser);

        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-shark'));
        $courseUser->setUser($this->getReference('user-trancara'));
        $courseUser->setGroup(1);
        $manager->persist($courseUser);

        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-shark'));
        $courseUser->setUser($this->getReference('user-de-boisc'));
        $courseUser->setGroup(1);
        $manager->persist($courseUser);

        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-shark'));
        $courseUser->setUser($this->getReference('user-muzardt'));
        $courseUser->setGroup(2);
        $manager->persist($courseUser);

        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-shark'));
        $courseUser->setUser($this->getReference('user-guerinh'));
        $courseUser->setGroup(1);
        $manager->persist($courseUser);

        $courseUser = new CourseUser();
        $courseUser->setCourse($this->getReference('course-rer'));
        $courseUser->setUser($this->getReference('user-muzardt'));
        $courseUser->setGroup(0);
        $manager->persist($courseUser);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CourseFixtures::class
        ];
    }
}
