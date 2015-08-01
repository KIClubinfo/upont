<?php

namespace KI\FoyerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\FoyerBundle\Entity\Youtube;


class LoadYoutubeFixture extends AbstractFixture implements OrderedFixtureInterface
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

    public function getOrder()
    {
        return 50;
    }
}
