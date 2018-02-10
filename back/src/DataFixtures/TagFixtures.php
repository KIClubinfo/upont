<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends Fixture
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
}
