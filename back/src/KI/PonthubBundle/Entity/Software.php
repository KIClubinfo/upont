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
class Software extends PonthubFile
{
    /**
     * Année
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;

    /**
     * Studio/développeur
     * @ORM\Column(name="author", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $author;

    /**
     * Version
     * @ORM\Column(name="version", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $version;

    /**
     * Operating System
     * @ORM\Column(name="os", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $os;

    /**
     * Set year
     *
     * @param integer $year
     * @return Game
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
     * Set author
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set os
     *
     * @param string $os
     *
     * @return Software
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

}
