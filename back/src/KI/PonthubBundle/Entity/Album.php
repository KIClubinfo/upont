<?php

namespace KI\PonthubBundle\Entity;

use KI\PonthubBundle\Entity\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Album extends PonthubFile
{
    /**
     * Artiste/Groupe
     * @ORM\Column(name="artist", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    protected $artist;

    /**
     * Année
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;

    /**
     * Liste des musiques
     * @ORM\OneToMany(targetEntity="KI\PonthubBundle\Entity\Music", mappedBy="album")
     * @Assert\Valid()
     */
    protected $musics;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->musics = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set artist
     *
     * @param string $artist
     * @return Album
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Music
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Add musics
     *
     * @param \KI\PonthubBundle\Entity\Music $musics
     * @return Album
     */
    public function addMusic(\KI\PonthubBundle\Entity\Music $musics)
    {
        $this->musics[] = $musics;

        return $this;
    }

    /**
     * Remove musics
     *
     * @param \KI\PonthubBundle\Entity\Music $musics
     */
    public function removeMusic(\KI\PonthubBundle\Entity\Music $musics)
    {
        $this->musics->removeElement($musics);
    }

    /**
     * Get musics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMusics()
    {
        return $this->musics;
    }

    /**
     * Set musics
     *
     * @return Album
     */
    public function setMusics($musics)
    {
        return $this->musics = $musics;
    }
}
