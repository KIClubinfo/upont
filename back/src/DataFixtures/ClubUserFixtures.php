<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\ClubUser;


class ClubUserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-archlinux'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Respo Web');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-taquet-c'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Prez\'');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-trancara'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Vieux Prez\'');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-de-boisc'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Prez\' Tech');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-muzardt'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Sec\' Gen, Respo LAN, Modérateur');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-ki'));
        $clubUser->setUser($this->getReference('user-guerinh'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Modérateur');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-mediatek'));
        $clubUser->setUser($this->getReference('user-muzardt'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Président');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-mediatek'));
        $clubUser->setUser($this->getReference('user-trancara'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Respo JdR');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-foyer'));
        $clubUser->setUser($this->getReference('user-peluchom'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Respo Boissons');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-bde'));
        $clubUser->setUser($this->getReference('user-dziris'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Respo Clubs');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-bda'));
        $clubUser->setUser($this->getReference('user-donat-bb'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Respo Opéra');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-pep'));
        $clubUser->setUser($this->getReference('user-guerinh'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('DRH');
        $manager->persist($clubUser);

        $clubUser = new ClubUser();
        $clubUser->setClub($this->getReference('club-gcc'));
        $clubUser->setUser($this->getReference('user-gcc'));
        $clubUser->setPriority($clubUser->getUser()->getId());
        $clubUser->setRole('Département');
        $manager->persist($clubUser);

        $manager->flush();
    }

    public function getOrder()
    {
        return 13;
    }
}
