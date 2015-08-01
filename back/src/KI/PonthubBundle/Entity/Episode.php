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
class Episode extends PonthubFile
{
    /**
     * Numéro de saison
     * @ORM\Column(name="season", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $season;

    /**
     * Numéro d'épisode
     * @ORM\Column(name="number", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $number;

    /**
     * Série parente
     * @ORM\ManyToOne(targetEntity="KI\PonthubBundle\Entity\Serie", inversedBy="episodes")
     * Comme on veut éviter que l'entité se join sur sa propre colonne
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $serie;

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->serie->image !== null ? $this->serie->image->getWebPath() : null;
    }







    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set season
     *
     * @param integer $season
     * @return Episode
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Episode
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set serie
     *
     * @param \KI\PonthubBundle\Entity\Serie $serie
     * @return episode
     */
    public function setSerie(\KI\PonthubBundle\Entity\Serie $serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \KI\PonthubBundle\Entity\Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }
}
