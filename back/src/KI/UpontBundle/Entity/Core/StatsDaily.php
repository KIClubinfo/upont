<?php

namespace KI\UpontBundle\Entity\Core;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Présente des statistiques journalières par promo
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class StatsDaily
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Promo (format: '0*', ie 016, 017...)
     * @ORM\Column(name="promo", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\Length(min=2,max=3)
     */
    protected $promo;

    /**
     * Verbes HTTP
     * @ORM\Column(name="httpVerbs", type="array")
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $httpVerbs = array();

    /**
     * Codes HTTP
     * @ORM\Column(name="httpCodes", type="array")
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $httpCodes = array();

    /**
     * Système d'exploitation
     * @ORM\Column(name="systems", type="array")
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $systems = array();

    /**
     * Navigateurs Web
     * @ORM\Column(name="browsers", type="array")
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $browsers = array();

    /**
     * Nombre total de connexions
     * @ORM\Column(name="connections", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $connections;

    /**
     * Nombre de connexions uniques
     * @ORM\Column(name="connectionsUnique", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $connectionsUnique;

    /**
     * Année
     * @ORM\Column(name="year", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $year;

    /**
     * Semaine de l'année
     * @ORM\Column(name="week", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $week;

    /**
     * Jour de l'année
     * @ORM\Column(name="day", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $day;

    // Incrémente une valeur d'un des tableaux ci-dessus
    public function increment($array, $value)
    {
        if (!property_exists($this, $array))
            return;
        $get = $this->$array;
        if (!array_key_exists($value, $get))
            $get[$value] = 0;
        $get[$value]++;
        $this->$array = $get;
    }

    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set promo
     *
     * @param string $promo
     * @return StatsDaily
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * Get promo
     *
     * @return string
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Set httpVerbs
     *
     * @param array $httpVerbs
     * @return StatsDaily
     */
    public function setHttpVerbs($httpVerbs)
    {
        $this->httpVerbs = $httpVerbs;

        return $this;
    }

    /**
     * Get httpVerbs
     *
     * @return array
     */
    public function getHttpVerbs()
    {
        return $this->httpVerbs;
    }

    /**
     * Set httpCodes
     *
     * @param array $httpCodes
     * @return StatsDaily
     */
    public function setHttpCodes($httpCodes)
    {
        $this->httpCodes = $httpCodes;

        return $this;
    }

    /**
     * Get httpCodes
     *
     * @return array
     */
    public function getHttpCodes()
    {
        return $this->httpCodes;
    }

    /**
     * Set systems
     *
     * @param array $systems
     * @return StatsDaily
     */
    public function setSystems($systems)
    {
        $this->systems = $systems;

        return $this;
    }

    /**
     * Get systems
     *
     * @return array
     */
    public function getSystems()
    {
        return $this->systems;
    }

    /**
     * Set browsers
     *
     * @param array $browsers
     * @return StatsDaily
     */
    public function setBrowsers($browsers)
    {
        $this->browsers = $browsers;

        return $this;
    }

    /**
     * Get browsers
     *
     * @return array
     */
    public function getBrowsers()
    {
        return $this->browsers;
    }

    /**
     * Set connections
     *
     * @param integer $connections
     * @return StatsDaily
     */
    public function setConnections($connections)
    {
        $this->connections = $connections;

        return $this;
    }

    /**
     * Get connections
     *
     * @return integer
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Set connectionsUnique
     *
     * @param integer $connectionsUnique
     * @return StatsDaily
     */
    public function setConnectionsUnique($connectionsUnique)
    {
        $this->connectionsUnique = $connectionsUnique;

        return $this;
    }

    /**
     * Get connectionsUnique
     *
     * @return integer
     */
    public function getConnectionsUnique()
    {
        return $this->connectionsUnique;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return StatsDaily
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
     * Set week
     *
     * @param integer $week
     * @return StatsDaily
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return integer
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return StatsDaily
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }
}
