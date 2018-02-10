<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Other;

class OtherFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $other = new Other();
        $other->setSize(1024*1024*1024);
        $other->setPath('/root/web/autres/windows vista.iso');
        $other->setName('Windows Vista');
        $other->setDescription('Aimez-vous vraiment l\'informatique?');
        $other->setTags([$this->getReference('tag-windaube'), $this->getReference('tag-merde'), $this->getReference('tag-daube')]);
        $other->setStatus('OK');
        $manager->persist($other);
        $this->addReference('other-windows-vista', $other);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class
        ];
    }
}
