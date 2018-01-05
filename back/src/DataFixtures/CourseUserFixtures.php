<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\CourseUser;


class CourseUserFixtures extends AbstractFixture implements OrderedFixtureInterface
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

    public function getOrder()
    {
        return 44;
    }
}
