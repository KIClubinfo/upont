<?php

namespace App\DataFixtures;

use App\Entity\Exercice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;


class ExerciceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $basePath = __DIR__ . '/../../tests/uploads/';
        $fs = new Filesystem();
        $fs->copy($basePath . 'file.pdf', $basePath . 'file_tmp.pdf');
        $file = new File($basePath . 'file_tmp.pdf');

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-mecastru'));
        $exercice->setUploader($this->getReference('user-dziris'));
        $exercice->setFile($file);
        $exercice->setName('Final 016');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-final-016', $exercice);

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-mecastru'));
        $exercice->setUploader($this->getReference('user-dziris'));
        $exercice->setFile($file);
        $exercice->setName('Final 015');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-final-015', $exercice);

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-mecastru'));
        $exercice->setUploader($this->getReference('user-dziris'));
        $exercice->setFile($file);
        $exercice->setName('Final 014');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-final-014', $exercice);

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-pipo'));
        $exercice->setUploader($this->getReference('user-muzardt'));
        $exercice->setFile($file);
        $exercice->setName('Apprendre à parler d\'un sujet que l\'on ne maitrise pas');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-pipo', $exercice);

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-shark'));
        $exercice->setUploader($this->getReference('user-kadaouic'));
        $exercice->setFile($file);
        $exercice->setName('Partiel 016');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-partiel-016', $exercice);

        $exercice = new Exercice();
        $exercice->setCourse($this->getReference('course-shark'));
        $exercice->setUploader($this->getReference('user-kadaouic'));
        $exercice->setFile($file);
        $exercice->setName('Partiel 016 (corrigé)');
        $exercice->setDate(time());
        $exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('exercice-partiel-016-corrige', $exercice);

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
