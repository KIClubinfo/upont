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
class Fix extends Likeable
{
    /**
     * Texte décrivant le problème
     * @ORM\Column(name="problem", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $problem;

    /**
     * Date de publication
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\GreaterThan(1)
     */
    protected $date;

    /**
     * Date de résolution
     * @ORM\Column(name="solved", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $solved;

    /**
     * Statut (Non vu|En attente|En cours|Résolu|Fermé)
     * @ORM\Column(name="status", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
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
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;

    /**
     * Responsables de la tâche
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="fix_respos",
     *      joinColumns={@ORM\JoinColumn(name="fix_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="respo_id", referencedColumnName="id")}
     * )
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $listRespos;





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
     * @param \KI\UpontBundle\Entity\Users\User $user
     *
     * @return Fix
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UpontBundle\Entity\Users\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add listRespo
     *
     * @param \KI\UpontBundle\Entity\Users\User $listRespo
     *
     * @return Fix
     */
    public function addListRespo(\KI\UpontBundle\Entity\Users\User $listRespo)
    {
        $this->listRespos[] = $listRespo;

        return $this;
    }

    /**
     * Remove listRespo
     *
     * @param \KI\UpontBundle\Entity\Users\User $listRespo
     */
    public function removeListRespo(\KI\UpontBundle\Entity\Users\User $listRespo)
    {
        $this->listRespos->removeElement($listRespo);
    }

    /**
     * Get listRespos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListRespos()
    {
        return $this->listRespos;
    }
}
