<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
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
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\Achievement")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $achievement;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", inversedBy="achievements")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $user;

    /**
     * L'Achievement a-t-il été vu ?
     * @ORM\Column(name="seen", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $seen;

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
     * @param \KI\UserBundle\Entity\Achievement $achievement
     * @return AchievementUser
     */
    public function setAchievement(\KI\UserBundle\Entity\Achievement $achievement)
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * Get achievement
     *
     * @return \KI\UserBundle\Entity\Achievement
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     * @return AchievementUser
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
     * Set seen
     *
     * @param boolean $seen
     * @return AchievementUser
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean
     */
    public function getSeen()
    {
        return $this->seen;
    }
}
