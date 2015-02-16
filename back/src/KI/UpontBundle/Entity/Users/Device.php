<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Définit un appareil (téléphone, tablette) enregistré pour recevoir des notifications push
 * @ORM\Entity
 * @ExclusionPolicy("all")
 * @UniqueEntity("device")
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
     * @ORM\Column(name="device", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $device;
    
    /**
     * Type (iOS|Android|WP)
     * @ORM\Column(name="type", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", inversedBy="devices")
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
     * @param \KI\UpontBundle\Entity\User $owner
     * @return Device
     */
    public function setOwner(\KI\UpontBundle\Entity\Users\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \KI\UpontBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
