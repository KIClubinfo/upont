<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class CourseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Groupe du membre
     * @ORM\Column(name="course_group", type="integer", nullable=false)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="KI\PublicationBundle\Entity\Course")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $course;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $user;

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
     * Set group
     *
     * @param string $group
     * @return CourseUser
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
     * Set course
     *
     * @param \KI\PublicationBundle\Entity\Course $course
     * @return CourseUser
     */
    public function setCourse(\KI\PublicationBundle\Entity\Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \KI\PublicationBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     * @return CourseUser
     */
    public function setUser(\KI\UserBundle\Entity\User $user)
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
}
