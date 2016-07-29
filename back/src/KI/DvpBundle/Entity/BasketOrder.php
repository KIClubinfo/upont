<?php

namespace KI\DvpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\DvpBundle\Entity\Basket;
use KI\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class BasketOrder
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * Prénom du client
     * @ORM\Column(name="firstName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $firstName;

    /**
     * Nom du client
     * @ORM\Column(name="lastName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $lastName;

    /**
     * Téléphone du client (au format 06.12.34.56.78)
     * @ORM\Column(name="phone", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $phone;

    /**
     * Adresse mail du client
     * @ORM\Column(name="email", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $email;

    /**
     * Date de la commande (timestamp)
     * @ORM\Column(name="dateOrder", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $dateOrder;

    /**
     * Date à laquelle la commande est censée être retirée (timestamp)
     * @ORM\Column(name="dateRetrieve", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $dateRetrieve;

    /**
     * Indique si la commande a été payée
     * @ORM\Column(name="paid", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $paid;

    /**
     * Client si dans uPont
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;

    /**
     * Panier choisi
     * @ORM\ManyToOne(targetEntity="KI\DvpBundle\Entity\Basket", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $basket;

    public function __construct()
    {
        $this->dateOrder = time();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return BasketOrder
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return BasketOrder
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return BasketOrder
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return BasketOrder
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateOrder
     *
     * @param integer $dateOrder
     *
     * @return BasketOrder
     */
    public function setDateOrder($dateOrder)
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    /**
     * Get dateOrder
     *
     * @return integer
     */
    public function getDateOrder()
    {
        return $this->dateOrder;
    }

    /**
     * Set dateRetrieve
     *
     * @param integer $dateRetrieve
     *
     * @return BasketOrder
     */
    public function setDateRetrieve($dateRetrieve)
    {
        $this->dateRetrieve = $dateRetrieve;

        return $this;
    }

    /**
     * Get dateRetrieve
     *
     * @return integer
     */
    public function getDateRetrieve()
    {
        return $this->dateRetrieve;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     *
     * @return BasketOrder
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return BasketOrder
     */
    public function setUser(User $user = null)
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

    /**
     * Set basket
     *
     * @param Basket $basket
     *
     * @return BasketOrder
     */
    public function setBasket(Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }
}
