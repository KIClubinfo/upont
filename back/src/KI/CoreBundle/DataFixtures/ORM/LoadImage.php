<?php

namespace KI\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\CoreBundle\Entity\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class LoadImageFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $images = array(
            'user-bochetc' => 'bochetc.jpg',
            'user-de-boisc' => 'de-boisc.jpg',
            'user-trancara' => 'trancara.jpg',
            'user-dziris' => 'dziris.jpg',
            'user-kadaouic' => 'kadaouic.jpg',
            'user-guerinh' => 'guerinh.jpg',
            'user-muzardt' => 'muzardt.jpg',
            'user-taquet-c' => 'taquet-c.jpg',
            'user-admissibles' => 'admissibles.png',
            'user-gcc' => 'gcc.jpg',
            'club-bda' => 'bda.jpg',
            'club-bde' => 'bde.jpg',
            'club-foyer' => 'foyer.jpg',
            'club-ki' => 'ki.png',
            'club-pep' => 'pep.png',
            'newsitem-git' => 'git.png',
            'newsitem-pulls' => 'pulls.jpg',
            'movie-pumping-iron' => 'pumping-iron.jpg',
            'game-age-of-empires-2' => 'age-of-empires-2.jpg',
            'software-vista' => 'vista.png',
            'supaero' => 'supaero.jpg'
        );

        $path = __DIR__ . '/../../../../../web/uploads/tests/';
        $fs = new Filesystem();

        foreach ($images as $tag => $name) {
            $fs->copy($path . $name, $path . 'tmp_' . $name);
            $file = new File($path . 'tmp_' . $name);
            $image = new Image();
            $image->setFile($file);
            $image->setExt($file->getExtension());
            $manager->persist($image);
            $this->addReference('image-' . $tag, $image);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
