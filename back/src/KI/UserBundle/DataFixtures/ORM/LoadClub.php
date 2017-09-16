<?php

namespace KI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UserBundle\Entity\Club;


class LoadClubFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $club = new Club();
        $club->setName('KI');
        $club->setFullName('Club Informatique');
        $club->setActive(true);
        $club->setCategory('club');
        $club->setIcon('download');
        $club->setImage($this->getReference('image-club-ki'));
        $club->setPresentation('Ce club est OMG fucking trop bien.');
        $manager->persist($club);
        $this->addReference('club-ki', $club);

        $club = new Club();
        $club->setName('BDE');
        $club->setFullName('Bureau Des Élèves');
        $club->setActive(true);
        $club->setCategory('asso');
        $club->setImage($this->getReference('image-club-bde'));
        $club->setBanner($this->getReference('image-supaero'));
        $manager->persist($club);
        $this->addReference('club-bde', $club);

        $club = new Club();
        $club->setName('BDA');
        $club->setFullName('Bureau Des Arts');
        $club->setActive(true);
        $club->setCategory('club');
        $club->setIcon('paint-brush');
        $club->setImage($this->getReference('image-club-bda'));
        $manager->persist($club);
        $this->addReference('club-bda', $club);

        $club = new Club();
        $club->setName('PEP');
        $club->setFullName('Ponts Études Projets');
        $club->setActive(true);
        $club->setCategory('asso');
        $club->setIcon('eur');
        $club->setImage($this->getReference('image-club-pep'));
        $manager->persist($club);
        $this->addReference('club-pep', $club);

        $club = new Club();
        $club->setName('Mediatek');
        $club->setFullName('Médiatek');
        $club->setActive(false);
        $club->setCategory('club');
        $manager->persist($club);
        $this->addReference('club-mediatek', $club);

        $club = new Club();
        $club->setName('Foyer');
        $club->setFullName('Foyer');
        $club->setCategory('club');
        $club->setActive(true);
        $club->setImage($this->getReference('image-club-foyer'));
        $manager->persist($club);
        $this->addReference('club-foyer', $club);

        $club = new Club();
        $club->setName('GCC');
        $club->setFullName('Génie Civil et Construction');
        $club->setActive(true);
        $club->setAdministration(true);
        $club->setImage($this->getReference('image-user-gcc'));
        $manager->persist($club);
        $this->addReference('club-gcc', $club);

        $club = new Club();
        $club->setName('La DDASS');
        $club->setFullName('La DDASS');
        $club->setActive(true);
        $club->setCategory('famille');
        $club->setAdministration(false);
        $club->setImage($this->getReference('image-club-la-ddass'));
        $club->setPresentation('La meilleure famille des Ponts ! <3');
        $manager->persist($club);
        $this->addReference('club-la-ddass', $club);

        $manager->flush();
    }

    public function getOrder()
    {
        return 12;
    }
}
