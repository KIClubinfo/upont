<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class AchievementUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Date d'obtention de l'achievement
     * @ORM\Column(name="date", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Achievement")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     */
    private $achievement;

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
     * Set date
     *
     * @param integer $date
     * @return AchievementUser
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set achievement
     *
     * @param \KI\UpontBundle\Entity\Achievement $achievement
     * @return AchievementUser
     */
    public function setAchievement(\KI\UpontBundle\Entity\Achievement $achievement)
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * Get achievement
     *
     * @return \KI\UpontBundle\Entity\Achievement 
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return AchievementUser
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
