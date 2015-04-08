<?php

namespace KI\UpontBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Tag;

class LoadTagFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setName('Bodybuilding');
        $manager->persist($tag);
        $this->addReference('tag-bodybuilding', $tag);

        $tag = new Tag();
        $tag->setName('Lift');
        $manager->persist($tag);
        $this->addReference('tag-lift', $tag);

        $tag = new Tag();
        $tag->setName('Poseeey');
        $manager->persist($tag);
        $this->addReference('tag-poseeey', $tag);

        $tag = new Tag();
        $tag->setName('Windaube');
        $manager->persist($tag);
        $this->addReference('tag-windaube', $tag);

        $tag = new Tag();
        $tag->setName('Merde');
        $manager->persist($tag);
        $this->addReference('tag-merde', $tag);

        $tag = new Tag();
        $tag->setName('Daubé');
        $manager->persist($tag);
        $this->addReference('tag-daube', $tag);

        $tag = new Tag();
        $tag->setName('Metaaal');
        $manager->persist($tag);
        $this->addReference('tag-metaaal', $tag);

        $manager->flush();
    }

    public function getOrder()
    {
        return 0;
    }
}
