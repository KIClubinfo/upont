<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Ponthub;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Ponthub\Music;

class LoadMusicFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Hells Bells');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Hells Bells.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Shoot To Thrill');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Shoot To Thrill.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('What Do You Do For Money Honey');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/What Do You Do For Money Honey.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Giving The Dog A Bone');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Giving The Dog A Bone.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Let Me Put My Love Into You');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Let Me Put My Love Into You.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Back In Black');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Back In Black.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('You Shook Me All Night Long');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/You Shook Me All Night Long.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Have A Drink On Me');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Have A Drink On Me.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Shake A Leg');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Shake A Leg.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Rock n Roll Aint Noise Pollution');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Back In Black/Rock n Roll Aint Noise Pollution.mp3');
        $music->setAlbum($this->getReference('album-back-in-black'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Rock Or Bust');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Rock Or Bust/Rock Or Bust.mp3');
        $music->setAlbum($this->getReference('album-rock-or-bust'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Play Ball');
        $music->setPath('/root/web/musique/Hard rock/AC_DC/Rock Or Bust/Play Ball.mp3');
        $music->setAlbum($this->getReference('album-rock-or-bust'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Enter Sandman');
        $music->setPath('/root/web/musique/Metal/Metallica/Black Album/Enter Sandman.mp3');
        $music->setAlbum($this->getReference('album-black-album'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $music = new Music();
        $music->setSize(8000000);
        $music->setName('Sad But True');
        $music->setPath('/root/web/musique/Metal/Metallica/Black Album/Sad But True.mp3');
        $music->setAlbum($this->getReference('album-black-album'));
        $music->setStatus('OK');
        $manager->persist($music);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 38;
    }
}
