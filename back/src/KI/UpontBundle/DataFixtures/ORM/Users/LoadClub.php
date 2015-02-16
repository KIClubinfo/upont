<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Club;


class LoadClubFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $club = new Club();
        $club->setShortName('KI');
        $club->setName('Club Informatique');
    	$club->setActive(true);
    	$club->setIcon('download');
    	$club->setImage($this->getReference('image-club-ki'));
        $manager->persist($club);
        $this->addReference('club-ki', $club);
        
        $club = new Club();
        $club->setShortName('BDE');
        $club->setName('Bureau Des Élèves');
    	$club->setActive(true);
    	$club->setImage($this->getReference('image-club-bde'));
        $manager->persist($club);
        $this->addReference('club-bde', $club);
        
        $club = new Club();
        $club->setShortName('BDA');
        $club->setName('Bureau Des Arts');
    	$club->setActive(true);
    	$club->setIcon('paint-brush');
    	$club->setImage($this->getReference('image-club-bda'));
        $manager->persist($club);
        $this->addReference('club-bda', $club);
        
        $club = new Club();
        $club->setShortName('PEP');
        $club->setName('Ponts Études Projets');
    	$club->setActive(true);
    	$club->setIcon('eur');
    	$club->setImage($this->getReference('image-club-pep'));
        $manager->persist($club);
        $this->addReference('club-pep', $club);
        
        $club = new Club();
        $club->setShortName('Mediatek');
        $club->setName('Médiatek');
    	$club->setActive(true);
        $manager->persist($club);
        $this->addReference('club-mediatek', $club);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 20;
    }
}
