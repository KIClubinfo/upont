<?php

namespace KI\UpontBundle\Entity\Ponthub;

use KI\UpontBundle\Entity\Ponthub\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Serie extends PonthubFile
{
    /**
     * Liste des épisodes
     * @ORM\OneToMany(targetEntity="KI\UpontBundle\Entity\Ponthub\Episode", mappedBy="serie")
     * @Assert\Valid()
     */
    protected $episodes;
    
    /**
     * Durée moyenne d'un épisode (en secondes)
     * @ORM\Column(name="duration", type="integer", nullable=true)
     * @Expose
     * @Assert\Range(min = 0, max = 86400)
     */
    protected $duration;
    
    /**
     * Année de début
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;
    
    /**
     * Son en VO ?
     * @ORM\Column(name="vo", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $vo;
    
    /**
     * Son en VF ?
     * @ORM\Column(name="vf", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $vf;
    
    /**
     * Sous-titres VO ?
     * @ORM\Column(name="vost", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $vost;
    
    /**
     * Sous-titres VF ?
     * @ORM\Column(name="vostfr", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $vostfr;
    
    /**
     * Version HD ?
     * @ORM\Column(name="hd", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $hd;
    
    /**
     * Réalisateur
     * @ORM\Column(name="director", type="string", nullable=true)
     * @Expose
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
     * @VirtualProperty()
     */
    public function actorsList()
    {
        $actors = array();
        foreach($this->actors as $actor)
            $actors[] = $actor->getName();
        return $actors;
    }
    
    /**
     * Score Metascore/Imdb (en %)
     * @ORM\Column(name="rating", type="integer", nullable=true)
     * @Expose
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
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set duration
     *
     * @param integer $duration
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * Add episodes
     *
     * @param \KI\UpontBundle\Entity\Ponthub\Episode $episode
     * @return Serie
     */
    public function addEpisode(\KI\UpontBundle\Entity\Ponthub\Episode $episode)
    {
        $this->episodes[] = $episode;

        return $this;
    }

    /**
     * Remove episodes
     *
     * @param \KI\UpontBundle\Entity\Ponthub\Episode $episodes
     */
    public function removeEpisode(\KI\UpontBundle\Entity\Ponthub\Episode $episode)
    {
        $this->episodes->removeElement($episode);
    }

    /**
     * Get episodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }
    
    /**
     * Set episodes
     *
     * @return Serie
     */
    public function setEpisodes($episodes)
    {
        return $this->episodes = $episodes;
    }
    
    /**
     * Set rating
     *
     * @param string $rating
     * @return Serie
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
