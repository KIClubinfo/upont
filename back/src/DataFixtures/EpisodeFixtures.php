<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $episode = new Episode();
        $episode->setPath('/root/web/series/How I met your mother/Saison 1/S01 E01 - Pilot.avi');
        $episode->setName('Pilot');
        $episode->setSeason(1);
        $episode->setNumber(1);
        $episode->setSize(700*1000*1000);
        $episode->setSerie($this->getReference('serie-himym'));
        $episode->setStatus('OK');
        $manager->persist($episode);
        $this->addReference('episode-pilot', $episode);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SerieFixtures::class,
        ];
    }
}
