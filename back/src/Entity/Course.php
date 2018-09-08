<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Course extends Likeable
{
    /**
     * Semestre (0: toute l'année, 1: premier, 2: second)
     * @ORM\Column(name="semester", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
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
     * Nombre d'ECTS
     * @ORM\Column(name="ects", type="float", nullable=true)
     * @JMS\Expose
     * @Assert\Type("float")
     */
    protected $ects;

    /**
     * Permet une sorte de modération
     * @ORM\Column(name="active", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $active;

    /**
     * Groupes de ce cours
     * @ORM\Column(name="course_groups", type="array", nullable=true)
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $groups = [];

    /**
     * Liste des annales de ce cours
     * @ORM\OneToMany(targetEntity="App\Entity\Exercice", mappedBy="course", cascade={"persist","remove"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $exercices;

    /**
     * Liste des occurrences de ce cours
     * @ORM\OneToMany(targetEntity="App\Entity\CourseItem", mappedBy="course", cascade={"persist","remove"})
     * @Assert\Valid()
     */
    protected $courseitems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->exercices = new ArrayCollection();
        $this->courseitems = new ArrayCollection();
    }

    /**
     * Set ects
     *
     * @param float $ects
     * @return Course
     */
    public function setEcts($ects)
    {
        $this->ects = $ects;

        return $this;
    }

    /**
     * Get ects
     *
     * @return float
     */
    public function getEcts()
    {
        return $this->ects;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Course
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set semester
     *
     * @param string $semester
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
     * @return string
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
     * @param string $group
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
     * @param string $group
     */
    public function removeGroup($group)
    {
        if (($key = array_search($group, $this->groups)) !== false) {
            unset($this->groups[$key]);
        }
        $this->groups = array_values($this->groups);
    }

    /**
     * Get groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add exercices
     *
     * @param \App\Entity\Exercice $exercices
     * @return Course
     */
    public function addExercice(\App\Entity\Exercice $exercices)
    {
        $this->exercices[] = $exercices;

        return $this;
    }

    /**
     * Remove exercices
     *
     * @param \App\Entity\Exercice $exercices
     */
    public function removeExercice(\App\Entity\Exercice $exercices)
    {
        $this->exercices->removeElement($exercices);
    }

    /**
     * Get exercices
     *
     * @return Collection
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
     * @param \App\Entity\Courseitem $courseitems
     * @return Course
     */
    public function addCourseitem(\App\Entity\Courseitem $courseitems)
    {
        $this->courseitems[] = $courseitems;

        return $this;
    }

    /**
     * Remove courseitems
     *
     * @param \App\Entity\Courseitem $courseitems
     */
    public function removeCourseitem(\App\Entity\Courseitem $courseitems)
    {
        $this->courseitems->removeElement($courseitems);
    }

    /**
     * Get courseitems
     *
     * @return Collection
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
