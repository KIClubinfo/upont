<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Ponthub;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Ponthub\Serie;

class LoadSerieFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $serie = new Serie();
        $serie->setName('How I Met Your Mother');
        $serie->setPath('/root/web/series/How I met your mother');
        $serie->setDescription('Ted searches for the woman of his dreams in New York City with the help of his four best friends.');
        $serie->setVf(false);
        $serie->setVost(true);
        $serie->setTags(array($this->getReference('tag-poseeey')));
        $serie->setDuration(1320);
        $serie->setRating(91);
        $serie->setDirector('Carter Bays');
        $serie->setStatus('OK');
        $manager->persist($serie);
        $manager->flush();
        $this->addReference('serie-himym', $serie);
    }
    
    public function getOrder()
    {
        return 35;
    }
}
