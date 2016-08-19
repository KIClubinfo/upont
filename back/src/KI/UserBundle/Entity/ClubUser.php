<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="KI\UserBundle\Repository\ClubUserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class ClubUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Rôle du membre
     * @ORM\Column(name="role", type="string", length=255)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\Club")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $user;

    /**
     * Priorité du membre pour l'affichage
     * @ORM\Column(name="priority", type="integer")
     * @JMS\Expose
     */
    private $priority;



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
     * Set role
     *
     * @param string $role
     * @return ClubUser
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set club
     *
     * @param \KI\UserBundle\Entity\Club $club
     * @return ClubUser
     */
    public function setClub(\KI\UserBundle\Entity\Club $club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return \KI\UserBundle\Entity\Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     * @return ClubUser
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

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return ClubUser
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
