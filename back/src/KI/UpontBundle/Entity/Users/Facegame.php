<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stocke un commentaire utilisateur
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Facegame
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * Date
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Durée du jeu
     * @ORM\Column(name="duration", type="integer", nullable = true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $duration;

    /**
     * Nombre de réponses fausses
     * @ORM\Column(name="wrongAnswers", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $wrongAnswers;

    /**
     * Joueur
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @JMS\Expose
     */
    protected $user;

    /**
     * Liste des utilisateurs représentés dans le jeu
     * @ORM\Column(name="listUsers", type="array")
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $listUsers;

    /**
     * Promo
     * @ORM\Column(name="promo", type="string", nullable = true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $promo;

    /**
     * Promo
     * @ORM\Column(name="hardcore", type="boolean", nullable = true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $hardcore;






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
     *
     * @return Facegame
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Facegame
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get wrongAnswers
     *
     * @return integer
     */
    public function getWrongAnswers()
    {
        return $this->wrongAnswers;
    }

    /**
     * Set wrongAnswers
     *
     * @param integer $wrongAnswers
     *
     * @return Facegame
     */
    public function setWrongAnswers($wrongAnswers)
    {
        $this->wrongAnswers = $wrongAnswers;

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
     * Set listUsers
     *
     * @param array $listUsers
     *
     * @return Facegame
     */
    public function setListUsers($listUsers)
    {
        $this->listUsers = $listUsers;

        return $this;
    }

    /**
     * Get listUsers
     *
     * @return array
     */
    public function getListUsers()
    {
        return $this->listUsers;
    }

    /**
     * Set promo
     *
     * @param string $promo
     *
     * @return Facegame
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * Get promo
     *
     * @return string
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Set mode
     *
     * @param boolean $hardcore
     *
     * @return Facegame
     */
    public function setHardcore($hardcore)
    {
        $this->hardcore = $hardcore;

        return $this;
    }

    /**
     * Get mode
     *
     * @return boolean
     */
    public function getHardcore()
    {
        return $this->hardcore;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\Users\User $user
     *
     * @return Facegame
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
}
