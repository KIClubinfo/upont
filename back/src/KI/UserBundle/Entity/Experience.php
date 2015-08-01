<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Experience extends Likeable
{
    /**
     * Début (timestamp)
     * @ORM\Column(name="startDate", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\GreaterThan(1)
     */
    protected $startDate;

    /**
     * Fin (timestamp)
     * @ORM\Column(name="endDate", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\GreaterThan(1)
     */
    protected $endDate;

    /**
     * Lieu du stage
     * @ORM\Column(name="city", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $city;

    /**
     * Lieu du stage
     * @ORM\Column(name="country", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $country;

    /**
     * Longitude
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @JMS\Expose
     * @Assert\Type("float")
     */
    protected $longitude;

    /**
     * Latitude
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @JMS\Expose
     * @Assert\Type("float")
     */
    protected $latitude;

    /**
     * Description
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $description;

    /**
     * Catégorie [PFE|Stage|Stage ouvrier|Stage scientifique|Stage long|Stage court]
     * @ORM\Column(name="category", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $category;

    /**
     * Chez qui on a fait le stage
     * @ORM\Column(name="company", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $company;

    /**
     * Personne ayant fait le stage
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;







    //===== GENERATED AUTOMATICALLY =====//
    /**
     * Set startDate
     *
     * @param integer $startDate
     * @return Experience
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param integer $endDate
     * @return Experience
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Experience
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Experience
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return Experience
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     * @return Experience
     */
    public function setUser(\KI\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Experience
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Experience
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Experience
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Experience
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }
}
