<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Reservation
{
    /**
    * @ORM\ManyToOne(targetEntity="KI\ClubinfoBundle\Entity\Centrale")
    * @ORM\JoinColumn(nullable=false)
    */
    private $centrale;

    /**
    * @ORM\OneToOne(targetEntity="KI\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;


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
     * Set number
     *
     * @param integer $number
     *
     * @return Reservation
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set centrale
     *
     * @param \KI\ClubinfoBundle\Entity\Centrale $centrale
     *
     * @return Reservation
     */
    public function setCentrale(\KI\ClubinfoBundle\Entity\Centrale $centrale)
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
     * @return Reservation
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
