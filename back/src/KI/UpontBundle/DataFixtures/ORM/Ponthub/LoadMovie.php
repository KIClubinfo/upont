<?php

namespace KI\UpontBundle\DataFixtures\ORM\Ponthub;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Ponthub\Movie;

class LoadMovieFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $movie = new Movie();
        $movie->setSize(1024*1024*1024);
        $movie->setPath('/root/web/films/Pumping Iron 1.mkv');
        $movie->setName('Pumping Iron');
        $movie->setDescription('Do you even lift?');
        $movie->setVf(false);
        $movie->setVost(true);
        $movie->setGenres(array($this->getReference('genre-sport'), $this->getReference('genre-documentary')));
        $movie->setActors(array($this->getReference('actor-arnold')));
        $movie->setTags(array($this->getReference('tag-bodybuilding'), $this->getReference('tag-lift')));
        $movie->setDuration(85*60);
        $movie->setRating(62);
        $movie->setLikes(array($this->getReference('user-de-boisc'), $this->getReference('user-muzardt')));
        $movie->setDislikes(array($this->getReference('user-taquet-c')));
        $movie->setStatus('OK');
        $movie->setAdded(time());
        $movie->setImage($this->getReference('image-movie-pumping-iron'));
        $manager->persist($movie);
        $this->addReference('movie-pumping-iron', $movie);

        $manager->flush();
    }

    public function getOrder()
    {
        return 41;
    }
}
