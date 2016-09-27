<?php

namespace KI\DvpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class BasketDate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * Date à laquelle la commande est censée être retirée
     * @ORM\Column(name="dateRetrieve", type="date", unique=true)
     * @JMS\Expose
     * @Assert\Type("DateTime")
     */
    protected $dateRetrieve;

    /**
     * Indique si la commande a été payée
     * @ORM\Column(name="locked", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $locked = false;

    /**
     * Commandes du jour
     * @ORM\OneToMany(targetEntity="KI\DvpBundle\Entity\BasketOrder", mappedBy="dateRetrieve")
     * @Assert\Valid()
     */
    protected $orders;

    /**
     * BasketDate constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return BasketDate
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRetrieve()
    {
        return $this->dateRetrieve;
    }

    /**
     * @param \DateTime $dateRetrieve
     * @return BasketDate
     */
    public function setDateRetrieve($dateRetrieve)
    {
        $this->dateRetrieve = $dateRetrieve;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return BasketDate
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     * @return BasketDate
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }


}
