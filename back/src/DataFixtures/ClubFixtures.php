<?php

namespace App\DataFixtures;

use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class ClubFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $club = new Club();
        $club->setName('KI');
        $club->setFullName('Club Informatique');
        $club->setActive(true);
        $club->setCategory('club-divertissement');
        $club->setIcon('download');
        $club->setImage($this->getReference('image-club-ki'));
        $club->setPresentation('Ce club est OMG fucking trop bien.');
        $club->setPlace('P401');
        $manager->persist($club);
        $this->addReference('club-ki', $club);

        $club = new Club();
        $club->setName('BDE');
        $club->setFullName('Bureau Des Élèves');
        $club->setActive(true);
        $club->setCategory('asso');
        $club->setImage($this->getReference('image-club-bde'));
        $club->setBanner($this->getReference('image-supaero'));
        $club->setPlace('V0??');
        $manager->persist($club);
        $this->addReference('club-bde', $club);

        $club = new Club();
        $club->setName('BDA');
        $club->setFullName('Bureau Des Arts');
        $club->setActive(true);
        $club->setCategory('club-artistique');
        $club->setIcon('paint-brush');
        $club->setImage($this->getReference('image-club-bda'));
        $club->setPlace('P2??');
        $manager->persist($club);
        $this->addReference('club-bda', $club);

        $club = new Club();
        $club->setName('PEP');
        $club->setFullName('Ponts Études Projets');
        $club->setActive(true);
        $club->setCategory('asso');
        $club->setIcon('eur');
        $club->setImage($this->getReference('image-club-pep'));
        $club->setPlace('P1??');
        $manager->persist($club);
        $this->addReference('club-pep', $club);

        $club = new Club();
        $club->setName('Mediatek');
        $club->setFullName('Médiatek');
        $club->setActive(false);
        $club->setCategory('club-divertissement');
        $manager->persist($club);
        $this->addReference('club-mediatek', $club);

        $club = new Club();
        $club->setName('Foyer');
        $club->setFullName('Foyer');
        $club->setCategory('club-divertissement');
        $club->setActive(true);
        $club->setImage($this->getReference('image-club-foyer'));
        $club->setPlace('Suivez l\'odeur');
        $manager->persist($club);
        $this->addReference('club-foyer', $club);

        $club = new Club();
        $club->setName('GCC');
        $club->setFullName('Génie Civil et Construction');
        $club->setCategory('autre');
        $club->setActive(true);
        $club->setAdministration(true);
        $club->setImage($this->getReference('image-user-gcc'));
        $manager->persist($club);
        $this->addReference('club-gcc', $club);

        $club = new Club();
        $club->setName('BPC');
        $club->setFullName('Brunch Ponts Club');
        $club->setCategory('club-gastronomique');
        $club->setActive(true);
        $club->setImage($this->getReference('image-club-bpc'));
        $manager->persist($club);
        $this->addReference('club-bpc', $club);

        $club = new Club();
        $club->setName('PMA');
        $club->setFullName('Ponts Monde Arabe');
        $club->setCategory('club-culturel');
        $club->setActive(true);
        $club->setImage($this->getReference('image-club-pma'));
        $manager->persist($club);
        $this->addReference('club-pma', $club);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ImageFixtures::class,
        ];
    }
}
