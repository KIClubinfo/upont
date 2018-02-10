<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SerieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $serie = new Serie();
        $serie->setName('How I Met Your Mother');
        $serie->setPath('/root/web/series/How I met your mother');
        $serie->setDescription('Ted searches for the woman of his dreams in New York City with the help of his four best friends.');
        $serie->setTags([$this->getReference('tag-poseeey')]);
        $serie->setDuration(1320);
        $serie->setRating(91);
        $serie->setDirector('Carter Bays');
        $serie->setStatus('OK');
        $manager->persist($serie);
        $manager->flush();
        $this->addReference('serie-himym', $serie);
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class
        ];
    }
}
