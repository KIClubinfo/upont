<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\PonthubFile;
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
     * Réalisateur
     * @ORM\Column(name="director", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $director;

    /**
     * Acteurs
     * @ORM\ManyToMany(targetEntity="App\Entity\Actor", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $actors;

    /**
     * @JMS\VirtualProperty()
     */
    public function actorsList()
    {
        $actors = [];
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
     * Add actor
     *
     * @param \App\Entity\Actor $actor
     * @return Movie
     */
    public function addActor(\App\Entity\Actor $actor)
    {
        $this->actors[] = $actor;

        return $this;
    }

    /**
     * Remove actor
     *
     * @param \App\Entity\Actor $actor
     */
    public function removeActor(\App\Entity\Actor $actor)
    {
        $this->actors->removeElement($actor);
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
