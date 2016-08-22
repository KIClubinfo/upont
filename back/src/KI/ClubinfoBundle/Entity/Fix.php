<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Fix extends Likeable
{
    /**
     * Texte décrivant le problème
     * @ORM\Column(name="problem", type="text")
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $problem;

    /**
     * Date de publication
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $date;

    /**
     * Date de résolution
     * @ORM\Column(name="solved", type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $solved;

    /**
     * Statut (Non vu|En attente|En cours|Résolu|Fermé)
     * @ORM\Column(name="status", type="string")
     * @JMS\Expose
     */
    protected $status;

    /**
     * S'il s'agit d'un dépannage (true) ou d'un message concernant uPont (false)
     * @ORM\Column(name="fix", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $fix;

    /**
     * Auteur de la réclamation
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;
    protected $autoSetUser = 'user';
    public function getAutoSetUser() { return $this->autoSetUser; }

    public function __construct()
    {
        parent::__construct();
        $this->date = time();
    }

    /**
     * Actualise la date de résolution
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateStatus()
    {
        $this->solved = $this->status === 'Résolu' ? time() : null;
    }

    /**
     * Set problem
     *
     * @param string $problem
     *
     * @return Fix
     */
    public function setProblem($problem)
    {
        $this->problem = $problem;

        return $this;
    }

    /**
     * Get problem
     *
     * @return string
     */
    public function getProblem()
    {
        return $this->problem;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return Fix
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
     * Set solved
     *
     * @param integer $solved
     *
     * @return Fix
     */
    public function setSolved($solved)
    {
        $this->solved = $solved;

        return $this;
    }

    /**
     * Get solved
     *
     * @return integer
     */
    public function getSolved()
    {
        return $this->solved;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Fix
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set fix
     *
     * @param bool $fix
     *
     * @return Fix
     */
    public function setFix($fix)
    {
        $this->fix = $fix;

        return $this;
    }

    /**
     * Get fix
     *
     * @return bool
     */
    public function getFix()
    {
        return $this->fix;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     *
     * @return Fix
     */
    public function setUser(\KI\UserBundle\Entity\User $user = null)
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
