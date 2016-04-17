<?php

namespace KI\FoyerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @JMS\Expose
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Valeur de l'échange
     * @ORM\Column(name="amount", type="float")
     * @JMS\Expose
     * @Assert\Type("numeric")
     */
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="KI\FoyerBundle\Entity\Beer")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Expose
     */
    private $beer;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $user;

    public function __construct()
    {
        $this->date = time();
    }

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
     * @return Transaction
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
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set beer
     *
     * @param \KI\FoyerBundle\Entity\Beer $beer
     *
     * @return Transaction
     */
    public function setBeer(\KI\FoyerBundle\Entity\Beer $beer)
    {
        $this->beer = $beer;

        return $this;
    }

    /**
     * Get beer
     *
     * @return \KI\FoyerBundle\Entity\Beer
     */
    public function getBeer()
    {
        return $this->beer;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     *
     * @return Transaction
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
