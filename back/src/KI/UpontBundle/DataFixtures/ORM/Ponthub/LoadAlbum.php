<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Ponthub;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Ponthub\Album;

class LoadAlbumFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $album = new Album();
        $album->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black');
        $album->setName('Back In Black');
        $album->setArtist('AC/DC');
        $album->setYear(1980);
        $album->setGenres(array($this->getReference('genre-hard-rock')));
        $album->setStatus('OK');
        $album->setImage($this->getReference('image-album-back-in-black'));
        $manager->persist($album);
        $this->addReference('album-back-in-black', $album);
        
        $album = new Album();
        $album->setPath('/root/web/musique/Hard rock/AC_DC/Rock Or Bust');
        $album->setName('Rock Or Bust');
        $album->setArtist('AC/DC');
        $album->setYear(2014);
        $album->setGenres(array($this->getReference('genre-hard-rock')));
        $album->setStatus('OK');
        $album->setImage($this->getReference('image-album-rock-or-bust'));
        $manager->persist($album);
        $this->addReference('album-rock-or-bust', $album);
        
        $album = new Album();
        $album->setPath('/root/web/musique/Hard rock/Metallica/Black Album');
        $album->setName('Black Album');
        $album->setArtist('Metallica');
        $album->setYear(1991);
        $album->setGenres(array($this->getReference('genre-metal')));
        $album->setTags(array($this->getReference('tag-metaaal')));
        $album->setStatus('OK');
        $manager->persist($album);
        $this->addReference('album-black-album', $album);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 37;
    }
}
