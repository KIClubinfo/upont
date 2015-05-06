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
     * @ORM\Column(name="problem", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $problem;

    /**
     * Réponse du respo
     * @ORM\Column(name="answer", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $answer;

    /**
     * Date de publication
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\NotBlank()
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
     * Statut
     * @ORM\Column(name="status", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * Catégorie (Bug report | Problème Internet | Problème matériel...)
     * @ORM\Column(name="category", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $category;

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
     * Set answer
     *
     * @param string $answer
     *
     * @return Fix
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
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
     * Set category
     *
     * @param string $category
     *
     * @return Fix
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
