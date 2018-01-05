<?php

namespace App\DataFixtures;

use App\Entity\Youtube;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class YoutubeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $youtube = new Youtube();
        $youtube->setName('Nyan Cat');
        $youtube->setLink('www.youtube.com/watch?v=QH2-TGUlwu4');
        $youtube->setDate(time() - 3600);
        $youtube->setUser($this->getReference('user-trancara'));
        $manager->persist($youtube);

        $youtube = new Youtube();
        $youtube->setName('Keyboard Cat');
        $youtube->setLink('https://www.youtube.com/watch?v=J---aiyznGQ');
        $youtube->setDate(time() - 3*3600);
        $youtube->setUser($this->getReference('user-de-boisc'));
        $manager->persist($youtube);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
