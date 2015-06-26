<?php

namespace KI\UpontBundle\DataFixtures\ORM\Publications;

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
        $course->addGroup('0');
        $course->setStartDate(3600*8.5);
        $course->setEndDate(3600*12);
        $course->setSemester(0);
        $course->setDepartment('SEGF');
        $this->getReference('user-kadaouic')->addCourse($course);
        $manager->persist($course);
        $this->addReference('course-shark', $course);

        $course = new Course();
        $course->setName('Pipeaulogie');
        $course->addGroup('3');
        $course->setStartDate(3600*8.5);
        $course->setEndDate(3600*9);
        $course->setSemester(1);
        $course->setDepartment('1A');
        $this->getReference('user-taquet-c')->addCourse($course);
        $manager->persist($course);
        $this->addReference('course-pipo', $course);

        $course = new Course();
        $course->setName('Mécanique des Structures');
        $course->addGroup('5');
        $course->setStartDate(3600*8.5);
        $course->setEndDate(3600*18);
        $course->setSemester(1);
        $course->setDepartment('GCC');
        $this->getReference('user-trancara')->addCourse($course);
        $this->getReference('user-de-boisc')->addCourse($course);
        $this->getReference('user-guerinh')->addCourse($course);
        $this->getReference('user-dziris')->addCourse($course);
        $manager->persist($course);
        $this->addReference('course-mecastru', $course);

        $course = new Course();
        $course->setName('Rabotage de quais de RER');
        $course->addGroup('0');
        $course->setStartDate(3600*8.5);
        $course->setEndDate(3600*12);
        $course->setSemester(2);
        $course->setDepartment('VET');
        $this->getReference('user-muzardt')->addCourse($course);
        $manager->persist($course);
        $this->addReference('course-rer', $course);

        $manager->flush();
    }

    public function getOrder()
    {
        return 15;
    }
}
