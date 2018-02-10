<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Code\Generator\DocBlock\Tag;

class MovieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $movie = new Movie();
        $movie->setSize(2.1*1000*1000*1000);
        $movie->setPath('/root/web/films/Pumping Iron 1.mkv');
        $movie->setName('Pumping Iron');
        $movie->setDescription('Do you even lift?');
        $movie->setGenres([$this->getReference('genre-sport'), $this->getReference('genre-documentary')]);
        $movie->setActors([$this->getReference('actor-arnold')]);
        $movie->setTags([$this->getReference('tag-bodybuilding'), $this->getReference('tag-lift')]);
        $movie->setDuration(85*60);
        $movie->setYear(1977);
        $movie->setRating(62);
        $movie->setLikes([$this->getReference('user-de-boisc'), $this->getReference('user-muzardt')]);
        $movie->setDislikes([$this->getReference('user-taquet-c')]);
        $movie->setStatus('OK');
        $movie->setAdded(time());
        $movie->setImage($this->getReference('image-movie-pumping-iron'));
        $manager->persist($movie);
        $this->addReference('movie-pumping-iron', $movie);

        $movie = new Movie();
        $movie->setSize(8.2*1000*1000*1000);
        $movie->setPath('/root/web/films/300.mkv');
        $movie->setName('300');
        $movie->setDescription('This is Sparta!');
        $movie->setGenres([$this->getReference('genre-action'), $this->getReference('genre-documentary')]);
        $movie->setDuration(85*60);
        $movie->setYear(2007);
        $movie->setRating(62);
        $movie->setLikes([$this->getReference('user-trancara'), $this->getReference('user-muzardt')]);
        $movie->setDislikes([$this->getReference('user-dziris')]);
        $movie->setStatus('OK');
        $movie->setAdded(time());
        $manager->persist($movie);
        $this->addReference('movie-300', $movie);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ImageFixtures::class,
            TagFixtures::class,
            ActorFixtures::class,
        ];
    }
}
