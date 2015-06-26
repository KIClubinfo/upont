<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Core\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Course extends Likeable
{
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
     * Groupes de ce cours
     * @ORM\Column(name="course_groups", type="array", nullable=true)
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $groups = array();

    /**
     * Liste des annales de ce cours
     * @ORM\OneToMany(targetEntity="KI\UpontBundle\Entity\Publications\Exercice", mappedBy="course")
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $exercices;

    /**
     * Liste des occurrences de ce cours
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
        $this->exercices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseitems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add groups
     *
     * @param string $groups
     * @return Course
     */
    public function addGroup($group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param string $groups
     */
    public function removeGroup($group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
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
