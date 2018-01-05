<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\CourseItem;


class CourseItemFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $courseItem = new CourseItem();
        $courseItem->setLocation('P 412');
        $courseItem->setStartDate(mktime(0, 0, 0, date('n'), date('j')));
        $courseItem->setEndDate(mktime(0, 0, 0, date('n'), date('j')) + 3600*10);
        $courseItem->setCourse($this->getReference('course-shark'));
        $courseItem->setGroup(2);
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('V 102');
        $courseItem->setStartDate(time() + 3600*2.5);
        $courseItem->setEndDate(time() + 3600*4);
        $courseItem->setCourse($this->getReference('course-shark'));
        $courseItem->setGroup(2);
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('B 307');
        $courseItem->setStartDate(time() + 3600*5);
        $courseItem->setEndDate(time() + 3600*6.5);
        $courseItem->setCourse($this->getReference('course-shark'));
        $courseItem->setGroup(2);
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('F 102');
        $courseItem->setStartDate(time() + 3600*3);
        $courseItem->setEndDate(time() + 3600*4.5);
        $courseItem->setCourse($this->getReference('course-pipo'));
        $courseItem->setGroup(2);
        $manager->persist($courseItem);

        $courseItem = new CourseItem();
        $courseItem->setLocation('Cauchy');
        $courseItem->setStartDate(time() + 3600*1);
        $courseItem->setEndDate(time() + 3600*3.5);
        $courseItem->setCourse($this->getReference('course-mecastru'));
        $courseItem->setGroup(0);
        $manager->persist($courseItem);

        $manager->flush();
    }

    public function getOrder()
    {
        return 43;
    }
}
