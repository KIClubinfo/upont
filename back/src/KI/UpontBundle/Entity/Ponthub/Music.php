<?php

namespace KI\UpontBundle\Entity\Ponthub;

use KI\UpontBundle\Entity\Ponthub\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Music extends PonthubFile
{
    /**
     * Album parent
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Ponthub\Album", inversedBy="musics")
     * Comme on veut éviter que l'entité se join sur sa propre colonne
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $album;
    
    
    
    
    
    
    
    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set album
     *
     * @param \KI\UpontBundle\Entity\Ponthub\Album $album
     * @return Music
     */
    public function setAlbum(\KI\UpontBundle\Entity\Ponthub\Album $album)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \KI\UpontBundle\Entity\Ponthub\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }
}
