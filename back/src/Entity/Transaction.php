<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
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
     * Nombre de bières
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $quantity = 1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Beer", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Expose
     */
    private $beer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
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
     * Get number
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set number
     *
     * @param integer $quantity
     *
     * @return Transaction
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Set beer
     *
     * @param Beer $beer
     *
     * @return Transaction
     */
    public function setBeer(Beer $beer)
    {
        $this->beer = $beer;

        return $this;
    }

    /**
     * Get beer
     *
     * @return Beer
     */
    public function getBeer()
    {
        return $this->beer;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Transaction
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
