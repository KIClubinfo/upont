<?php

namespace KI\UpontBundle\Entity\Users;

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
     * @ORM\Column(name="course_group", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Publications\Course")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $course;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $user;



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
     * @param \KI\UpontBundle\Entity\Course $course
     * @return CourseUser
     */
    public function setCourse(\KI\UpontBundle\Entity\Publications\Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \KI\UpontBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return CourseUser
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UpontBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
