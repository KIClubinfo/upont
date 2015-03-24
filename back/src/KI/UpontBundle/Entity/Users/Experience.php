<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Core\Likeable;

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
     * @ORM\Column(name="location", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $location;

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
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User")
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
     * Set location
     *
     * @param string $location
     * @return Experience
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
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
     * @param \KI\UpontBundle\Entity\Users\User $user
     * @return Experience
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UpontBundle\Entity\Users\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
