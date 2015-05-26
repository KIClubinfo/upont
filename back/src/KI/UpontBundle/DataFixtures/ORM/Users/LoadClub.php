<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Club;


class LoadClubFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $club = new Club();
        $club->setName('KI');
        $club->setFullName('Club Informatique');
        $club->setActive(true);
        $club->setIcon('download');
        $club->setImage($this->getReference('image-club-ki'));
        $club->setPresentation('Ce club est OMG fucking trop bien.');
        $manager->persist($club);
        $this->addReference('club-ki', $club);

        $club = new Club();
        $club->setName('BDE');
        $club->setFullName('Bureau Des Élèves');
        $club->setActive(true);
        $club->setImage($this->getReference('image-club-bde'));
        $club->setBanner($this->getReference('image-supaero'));
        $manager->persist($club);
        $this->addReference('club-bde', $club);

        $club = new Club();
        $club->setName('BDA');
        $club->setFullName('Bureau Des Arts');
        $club->setActive(true);
        $club->setIcon('paint-brush');
        $club->setImage($this->getReference('image-club-bda'));
        $manager->persist($club);
        $this->addReference('club-bda', $club);

        $club = new Club();
        $club->setName('PEP');
        $club->setFullName('Ponts Études Projets');
        $club->setActive(true);
        $club->setIcon('eur');
        $club->setImage($this->getReference('image-club-pep'));
        $manager->persist($club);
        $this->addReference('club-pep', $club);

        $club = new Club();
        $club->setName('Mediatek');
        $club->setFullName('Médiatek');
        $club->setActive(true);
        $manager->persist($club);
        $this->addReference('club-mediatek', $club);

        $club = new Club();
        $club->setName('GCC');
        $club->setFullName('Génie Civil et Construction');
        $club->setActive(false);
        $club->setAdministration(true);
        $club->setImage($this->getReference('image-user-gcc'));
        $manager->persist($club);
        $this->addReference('club-gcc', $club);

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}
