<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
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
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\Club")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     */
    private $user;



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
     * @param \KI\UpontBundle\Entity\Club $club
     * @return ClubUser
     */
    public function setClub(\KI\UpontBundle\Entity\Users\Club $club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return \KI\UpontBundle\Entity\Club 
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return ClubUser
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
