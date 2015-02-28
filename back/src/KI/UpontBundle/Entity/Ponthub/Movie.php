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
class Movie extends PonthubFile
{
    /**
     * Durée (en secondes)
     * @ORM\Column(name="duration", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 0, max = 86400)
     */
    protected $duration;

    /**
     * Année
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;

    /**
     * Son en VO ?
     * @ORM\Column(name="vo", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $vo;

    /**
     * Son en VF ?
     * @ORM\Column(name="vf", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $vf;

    /**
     * Sous-titres VO ?
     * @ORM\Column(name="vost", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $vost;

    /**
     * Sous titres VF ?
     * @ORM\Column(name="vostfr", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $vostfr;

    /**
     * Version HD ?
     * @ORM\Column(name="hd", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $hd;

    /**
     * Réalisateur
     * @ORM\Column(name="director", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $director;

    /**
     * Acteurs
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Ponthub\Actor", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $actors;

    /**
     * @JMS\VirtualProperty()
     */
    public function actorsList()
    {
        $actors = array();
        foreach ($this->actors as $actor)
            $actors[] = $actor->getName();
        return $actors;
    }

    /**
     * Score Metascore/Imdb (en %)
     * @ORM\Column(name="rating", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 0, max = 100)
     */
    protected $rating;







    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->actors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Movie
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movie
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
     * Set vo
     *
     * @param boolean $vo
     * @return Movie
     */
    public function setVo($vo)
    {
        $this->vo = $vo;

        return $this;
    }

    /**
     * Get vo
     *
     * @return boolean
     */
    public function getVo()
    {
        return $this->vo;
    }

    /**
     * Set vf
     *
     * @param boolean $vf
     * @return Movie
     */
    public function setVf($vf)
    {
        $this->vf = $vf;

        return $this;
    }

    /**
     * Get vf
     *
     * @return boolean
     */
    public function getVf()
    {
        return $this->vf;
    }

    /**
     * Set vost
     *
     * @param boolean $vost
     * @return Movie
     */
    public function setVost($vost)
    {
        $this->vost = $vost;

        return $this;
    }

    /**
     * Get vost
     *
     * @return boolean
     */
    public function getVost()
    {
        return $this->vost;
    }

    /**
     * Set vostfr
     *
     * @param boolean $vostfr
     * @return Movie
     */
    public function setVostfr($vostfr)
    {
        $this->vostfr = $vostfr;

        return $this;
    }

    /**
     * Get vostfr
     *
     * @return boolean
     */
    public function getVostfr()
    {
        return $this->vostfr;
    }

    /**
     * Set hd
     *
     * @param boolean $hd
     * @return Movie
     */
    public function setHd($hd)
    {
        $this->hd = $hd;

        return $this;
    }

    /**
     * Get hd
     *
     * @return boolean
     */
    public function getHd()
    {
        return $this->hd;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Movie
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set actors
     *
     * @param array $actors
     * @return Movie
     */
    public function setActors($actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return array
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * Set rating
     *
     * @param string $rating
     * @return Movie
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }
}
