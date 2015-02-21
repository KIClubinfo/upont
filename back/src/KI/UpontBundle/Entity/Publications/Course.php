<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Course extends Likeable
{
    /**
     * Groupe du cours (0 si pas de groupe)
     * @ORM\Column(name="course_group", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $group;

    /**
     * Heure de début du cours (secondes depuis 00:00:00)
     * @ORM\Column(name="startDate", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $startDate;

    /**
     * Heure de fin du cours (secondes depuis 00:00:00)
     * @ORM\Column(name="endDate", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $endDate;

    /**
     * Semestre (0: toute l'année, 1: premier, 2: second)
     * @ORM\Column(name="semester", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $semester;

    /**
     * Département
     * @ORM\Column(name="department", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $department;

    /**
     * Personnes suivant ce cours
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", mappedBy="courses", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $attendees;

    /**
     * Liste des annales de ce cours
     * @ORM\OneToMany(targetEntity="KI\UpontBundle\Entity\Publications\Exercice", mappedBy="course")
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $exercices;

    /**
     * Liste des annales de ce cours
     * @ORM\OneToMany(targetEntity="KI\UpontBundle\Entity\Publications\CourseItem", mappedBy="course")
     * @Assert\Valid()
     */
    protected $courseitems;

    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attendees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->exercices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseitems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set group
     *
     * @param string $group
     * @return Course
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
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

    /**
     * Add exercices
     *
     * @param \KI\UpontBundle\Entity\Publications\Exercice $exercices
     * @return Course
     */
    public function addExercice(\KI\UpontBundle\Entity\Publications\Exercice $exercices)
    {
        $this->exercices[] = $exercices;

        return $this;
    }

    /**
     * Remove exercices
     *
     * @param \KI\UpontBundle\Entity\Publications\Exercice $exercices
     */
    public function removeExercice(\KI\UpontBundle\Entity\Publications\Exercice $exercices)
    {
        $this->exercices->removeElement($exercices);
    }

    /**
     * Get exercices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExercices()
    {
        return $this->exercices;
    }

    /**
     * Set exercices
     *
     * @return Course
     */
    public function setExercices($exercices)
    {
        return $this->exercices = $exercices;
    }

    /**
     * Add courseitems
     *
     * @param \KI\UpontBundle\Entity\Publications\Courseitem $courseitems
     * @return Course
     */
    public function addCourseitem(\KI\UpontBundle\Entity\Publications\Courseitem $courseitems)
    {
        $this->courseitems[] = $courseitems;

        return $this;
    }

    /**
     * Remove courseitems
     *
     * @param \KI\UpontBundle\Entity\Publications\Courseitem $courseitems
     */
    public function removeCourseitem(\KI\UpontBundle\Entity\Publications\Courseitem $courseitems)
    {
        $this->courseitems->removeElement($courseitems);
    }

    /**
     * Get courseitems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseitems()
    {
        return $this->courseitems;
    }

    /**
     * Set courseitems
     *
     * @return Course
     */
    public function setCourseitems($courseitems)
    {
        return $this->courseitems = $courseitems;
    }
}
