<?php

namespace App\DataFixtures;

use App\Entity\Software;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SoftwareFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $software = new Software();
        $software->setSize(487864000);
        $software->setPath('/root/web/softs/WVista.rar');
        $software->setName('Windows 8');
        $software->setDescription('C\'est tout pourri mais bon...');
        $software->setTags([$this->getReference('tag-windaube')]);
        $software->setStatus('OK');
        $software->setImage($this->getReference('image-software-vista'));
        $software->setOs('Windows');
        $manager->persist($software);
        $this->addReference('software-windows', $software);

        $software = new Software();
        $software->setSize(1234567);
        $software->setPath('/root/web/softs/MACOSX.rar');
        $software->setName('Mac OSX');
        $software->setDescription('C\'est encore plus pourri mais bon...');
        $software->setStatus('OK');
        $software->setOs('Mac');
        $manager->persist($software);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class,
            ImageFixtures::class,
        ];
    }
}
