<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\Exercice;


class LoadExerciceFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $exercice = new Exercice();
        $exercice->setDepartment('1A');
        $exercice->setUploader($this->getReference('user-dziris'));
        $exercice->setName('Test');
        $exercice->setDate(time());
    	$exercice->setValid(false);
        $manager->persist($exercice);
        $this->addReference('1a-test', $exercice);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 25;
    }
}
