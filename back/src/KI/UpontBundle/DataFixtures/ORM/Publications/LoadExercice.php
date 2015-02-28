<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\Exercice;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;


class LoadExerciceFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $basePath = __DIR__ . '/../../../../../../web/uploads/tmp/';
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

    public function getOrder()
    {
        return 17;
    }
}
