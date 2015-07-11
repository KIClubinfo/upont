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
        $course->setSemester('Ouverture');
        $course->setActive(true);
        $course->setEcts(1.5);
        $course->setDepartment('SEGF');
        $manager->persist($course);
        $this->addReference('course-shark', $course);

        $course = new Course();
        $course->setName('Pipeaulogie');
        $course->addGroup('1');
        $course->addGroup('2');
        $course->addGroup('3');
        $course->setSemester('1er Semestre');
        $course->setActive(true);
        $course->setEcts(1);
        $course->setDepartment('1A');
        $manager->persist($course);
        $this->addReference('course-pipo', $course);

        $course = new Course();
        $course->setName('Mécanique des Structures');
        $course->addGroup('5');
        $course->setActive(false);
        $course->setEcts(3);
        $course->setSemester('2nd Semestre');
        $course->setDepartment('GCC');
        $manager->persist($course);
        $this->addReference('course-mecastru', $course);

        $course = new Course();
        $course->setName('Rabotage de quais de RER');
        $course->addGroup('0');
        $course->setEcts(4.5);
        $course->setDepartment('VET');
        $manager->persist($course);
        $this->addReference('course-rer', $course);

        $manager->flush();
    }

    public function getOrder()
    {
        return 15;
    }
}
