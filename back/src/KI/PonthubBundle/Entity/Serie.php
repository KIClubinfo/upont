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
class Serie extends PonthubFile
{
    /**
     * Liste des épisodes
     * @ORM\OneToMany(targetEntity="KI\PonthubBundle\Entity\Episode", mappedBy="serie")
     * @Assert\Valid()
     */
    protected $episodes;

    /**
     * Durée moyenne d'un épisode (en secondes)
     * @ORM\Column(name="duration", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 0, max = 86400)
     */
    protected $duration;

    /**
     * Année de début
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
     * Sous-titres VF ?
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
     * @ORM\ManyToMany(targetEntity="KI\PonthubBundle\Entity\Actor", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $actors;

    /**
     * @JMS\VirtualProperty()
     */
    public function actorsList()
    {
        $actors = array();
        foreach ($this->actors as $actor) {
                    $actors[] = $actor->getName();
        }
        return $actors;
    }

    /**
     * Score Metascore/Imdb (en %)
     * @ORM\Column(name="rating", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 0, max = 100)
     */
    protected $rating;

    /**
     * On retourne les vues du premier episode
     * (retourner le total de toutes les vues est trop couteux pour le back, cela
     * est fait par le front au cas par cas).
     * @JMS\VirtualProperty()
     */
    public function downloads()
    {
        $episodes = $this->getEpisodes();

        foreach ($episodes as $episode) {
            if ($episode->getSeason() == 1 && $episode->getNumber() == 1)
                return count($episode->getUsers());
        }
        return 0;
    }







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
     * Add actor
     *
     * @param \KI\PonthubBundle\Entity\Actor $actor
     * @return Serie
     */
    public function addActor(\KI\PonthubBundle\Entity\Actor $actor)
    {
        $this->actors[] = $actor;

        return $this;
    }

    /**
     * Remove actor
     *
     * @param \KI\PonthubBundle\Entity\Actor $actor
     */
    public function removeActor(\KI\PonthubBundle\Entity\Actor $actor)
    {
        $this->actors->removeElement($actor);
    }

    /**
     * Add episodes
     *
     * @param \KI\PonthubBundle\Entity\Episode $episode
     * @return Serie
     */
    public function addEpisode(\KI\PonthubBundle\Entity\Episode $episode)
    {
        $this->episodes[] = $episode;

        return $this;
    }

    /**
     * Remove episodes
     *
     * @param \KI\PonthubBundle\Entity\Episode $episode
     */
    public function removeEpisode(\KI\PonthubBundle\Entity\Episode $episode)
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
