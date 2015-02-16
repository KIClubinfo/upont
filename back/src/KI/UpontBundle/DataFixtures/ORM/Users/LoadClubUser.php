<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\ClubUser;


class LoadClubUserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-taquet-c'));
    	$clubUser->setRole('Prez\'');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-trancara'));
    	$clubUser->setRole('Prez\'');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-de-boisc'));
    	$clubUser->setRole('Prez\' Tech');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-muzardt'));
    	$clubUser->setRole('Sec\' Gen, Respo LAN, Modérateur');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-guerinh'));
    	$clubUser->setRole('Modérateur');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-mediatek'));
        $clubUser->setUser($this->getReference('user-muzardt'));
    	$clubUser->setRole('Président');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-mediatek'));
        $clubUser->setUser($this->getReference('user-trancara'));
    	$clubUser->setRole('Respo JdR');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-bde'));
        $clubUser->setUser($this->getReference('user-dziris'));
    	$clubUser->setRole('Respo Clubs');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-bda'));
        $clubUser->setUser($this->getReference('user-donat-bb'));
    	$clubUser->setRole('Respo Opéra');
        $manager->persist($clubUser);
        
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-pep'));
        $clubUser->setUser($this->getReference('user-guerinh'));
    	$clubUser->setRole('DRH');
        $manager->persist($clubUser);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 21;
    }
}
