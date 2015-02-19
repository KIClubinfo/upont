<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\CourseItem;


class LoadCourseItemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $courseItem = new CourseItem();
        $courseItem->setLocation('P 412');
        $courseItem->setStartDate(time());
        $courseItem->setEndDate(time() + 3600 * 1.5);
        $courseItem->setCourse($this->getReference('course-shark'));
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('V 102');
        $courseItem->setStartDate(time() + 3600 * 1.5);
        $courseItem->setEndDate(time() + 3600 * 3);
        $courseItem->setCourse($this->getReference('course-shark'));
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('B 307');
        $courseItem->setStartDate(time() + 3600 * 3);
        $courseItem->setEndDate(time() + 3600 * 4.5);
        $courseItem->setCourse($this->getReference('course-shark'));
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('F 102');
        $courseItem->setCourse($this->getReference('course-pipo'));
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('Cauchy');
        $courseItem->setCourse($this->getReference('course-mecastru'));
        $manager->persist($courseItem);

        $manager->flush();
    }

    public function getOrder()
    {
        return 16;
    }
}
