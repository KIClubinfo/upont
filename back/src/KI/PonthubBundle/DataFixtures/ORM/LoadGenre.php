<?php

namespace KI\PonthubBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PonthubBundle\Entity\Genre;

class LoadGenreFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $genre = new Genre();
        $genre->setName('Documentary');
        $manager->persist($genre);
        $this->addReference('genre-documentary', $genre);

        $genre = new Genre();
        $genre->setName('Sport');
        $manager->persist($genre);
        $this->addReference('genre-sport', $genre);

        $genre = new Genre();
        $genre->setName('Action');
        $manager->persist($genre);
        $this->addReference('genre-action', $genre);

        $genre = new Genre();
        $genre->setName('Horror');
        $manager->persist($genre);
        $this->addReference('genre-horror', $genre);

        $genre = new Genre();
        $genre->setName('Hard rock');
        $manager->persist($genre);
        $this->addReference('genre-hard-rock', $genre);

        $genre = new Genre();
        $genre->setName('Metal');
        $manager->persist($genre);
        $this->addReference('genre-metal', $genre);

        $manager->flush();
    }

    public function getOrder()
    {
        return 21;
    }
}
