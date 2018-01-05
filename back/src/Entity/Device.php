<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Définit un appareil (téléphone, tablette) enregistré pour recevoir des notifications push
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Identifiant du téléphone
     * @ORM\Column(name="device", type="string", unique=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $device;

    /**
     * Type (iOS|Android|WP)
     * @ORM\Column(name="type", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $owner;







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
     * Set device
     *
     * @param string $device
     * @return Device
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Device
     */
    public function setType($type)
    {
        if ($type == 'iOS' || $type == 'WP' || $type == 'Android')
            $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set owner
     *
     * @param \App\Entity\User $owner
     * @return Device
     */
    public function setOwner(\App\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \App\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
