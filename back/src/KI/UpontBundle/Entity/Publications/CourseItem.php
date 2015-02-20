<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class CourseItem
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Salle de cours/amphi
     * @ORM\Column(name="location", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $location;

    /**
     * Heure de début du cours (timestamp)
     * @ORM\Column(name="startDate", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $startDate;

    /**
     * Heure de fin du cours (timestamp)
     * @ORM\Column(name="endDate", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $endDate;

    /**
     * Le cours parent
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Publications\Course", inversedBy="courseitems")
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $course;

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
     * Set location
     *
     * @param string $location
     * @return CourseItem
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
     * Set startDate
     *
     * @param integer $startDate
     * @return CourseItem
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
     * @return CourseItem
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
     * Set course
     *
     * @param \KI\UpontBundle\Entity\Publications\Course $course
     * @return CourseItem
     */
    public function setCourse(\KI\UpontBundle\Entity\Publications\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \KI\UpontBundle\Entity\Publications\Course
     */
    public function getCourse()
    {
        return $this->course;
    }
}
