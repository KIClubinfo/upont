<?php

namespace KI\UpontBundle\Entity\Ponthub;

use KI\UpontBundle\Entity\Ponthub\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
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

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->album->image !== null ? $this->album->image->getWebPath() : null;
    }







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
