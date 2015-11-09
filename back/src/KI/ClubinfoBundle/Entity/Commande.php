<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Commande
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */

    protected $id;
    /**
     * Quantité comandée
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $quantity;

    /**
     * Centrale de cette commande
     * @ORM\ManyToOne(targetEntity="KI\ClubinfoBundle\Entity\Centrale", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $centrale;

    /**
     * Auteur de la commande
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;

    protected $autoSetUser = 'user';
    public function getAutoSetUser() { return $this->autoSetUser; }

    /**
     * Comande retirée ou non
     * @ORM\Column(name="taken", type="boolean", nullable=false)
     * @JMS\Expose
     */
    protected $taken;

    /**
     * Comande payée ou non
     * @ORM\Column(name="paid", type="boolean", nullable=false)
     * @JMS\Expose
     */
    protected $paid;

    /**
     * Get id
     *
     * @return integer
     */
    public function getSlug()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     *
     * @return Commande
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set centrale
     *
     * @param \KI\ClubinfoBundle\Entity\Centrale $centrale
     *
     * @return Commande
     */
    public function setCentrale(\KI\ClubinfoBundle\Entity\Centrale $centrale = null)
    {
        $this->centrale = $centrale;

        return $this;
    }

    /**
     * Get centrale
     *
     * @return \KI\ClubinfoBundle\Entity\Centrale
     */
    public function getCentrale()
    {
        return $this->centrale;
    }

    /**
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     *
     * @return Commande
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

    /**
     * Set taken
     *
     * @param string $taken
     *
     * @return Commande
     */
    public function setTaken($taken)
    {
        $this->taken = $taken;

        return $this;
    }

    /**
     * Get taken
     *
     * @return string
     */
    public function getTaken()
    {
        return $this->taken;
    }

    /**
     * Set paid
     *
     * @param string $paid
     *
     * @return Commande
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return string
     */
    public function getPaid()
    {
        return $this->paid;
    }



}
