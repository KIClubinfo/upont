<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\PonthubFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Serie", inversedBy="episodes")
     * Comme on veut éviter que l'entité se join sur sa propre colonne
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $serie;

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
     * @param \App\Entity\Serie $serie
     * @return episode
     */
    public function setSerie(\App\Entity\Serie $serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \App\Entity\Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }
}
