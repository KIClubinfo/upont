<?php

namespace KI\UpontBundle\Entity\Publications;

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
class Course
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Nom du cours
     * @ORM\Column(name="name", type="string")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * Heure de début du cours (secondes depuis 00:00:00)
     * @ORM\Column(name="startDate", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $startDate;

    /**
     * Heure de fin du cours (secondes depuis 00:00:00)
     * @ORM\Column(name="endDate", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $endDate;

    /**
     * Semestre (0: toute l'année, 1: premier, 2: second)
     * @ORM\Column(name="semester", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $semester;

    /**
     * Département
     * @ORM\Column(name="department", type="string")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $department;

    /**
     * Slug
     * @Gedmo\Slug(fields={"department","name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;

    /**
     * Personnes suivant ce cours
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", mappedBy="courses", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $attendees;

    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendees = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Course
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set startDate
     *
     * @param integer $startDate
     * @return Course
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
     * @return Course
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
     * Set semester
     *
     * @param integer $semester
     * @return Course
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * Get semester
     *
     * @return integer
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return Course
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Course
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add attendees
     *
     * @param \KI\UpontBundle\Entity\Users\User $attendees
     * @return Course
     */
    public function addAttendee(\KI\UpontBundle\Entity\Users\User $attendees)
    {
        $this->attendees[] = $attendees;

        return $this;
    }

    /**
     * Remove attendees
     *
     * @param \KI\UpontBundle\Entity\Users\User $attendees
     */
    public function removeAttendee(\KI\UpontBundle\Entity\Users\User $attendees)
    {
        $this->attendees->removeElement($attendees);
    }

    /**
     * Get attendees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttendees()
    {
        return $this->attendees;
    }
}
