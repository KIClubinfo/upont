<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\Course;


class LoadCourseFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $course = new Course();
        $course->setName('Devenir Shark en 5 étapes');
        $course->setGroup(0);
        $course->setStartDate(3600 * 8.5);
        $course->setEndDate(3600 * 12);
        $course->setSemester(0);
        $course->setDepartment('SEGF');
        $course->addAttendee($this->getReference('user-kadaouic'));
        $manager->persist($course);
        $this->addReference('course-shark', $course);

        $course = new Course();
        $course->setName('Pipeaulogie');
        $course->setGroup(3);
        $course->setStartDate(3600 * 8.5);
        $course->setEndDate(3600 * 9);
        $course->setSemester(1);
        $course->setDepartment('1A');
        $course->addAttendee($this->getReference('user-taquet-c'));
        $manager->persist($course);
        $this->addReference('course-pipo', $course);

        $course = new Course();
        $course->setName('Mécanique des Structures');
        $course->setGroup(5);
        $course->setStartDate(3600 * 8.5);
        $course->setEndDate(3600 * 18);
        $course->setSemester(1);
        $course->setDepartment('GCC');
        $course->addAttendee($this->getReference('user-trancara'));
        $course->addAttendee($this->getReference('user-guerinh'));
        $course->addAttendee($this->getReference('user-dziris'));
        $course->addAttendee($this->getReference('user-de-boisc'));
        $manager->persist($course);
        $this->addReference('course-mecastru', $course);

        $course = new Course();
        $course->setName('Rabotage de quais de RER');
        $course->setGroup(0);
        $course->setStartDate(3600 * 8.5);
        $course->setEndDate(3600 * 12);
        $course->setSemester(2);
        $course->setDepartment('VET');
        $course->addAttendee($this->getReference('user-muzardt'));
        $manager->persist($course);
        $this->addReference('course-rer', $course);

        $manager->flush();
    }

    public function getOrder()
    {
        return 15;
    }
}
