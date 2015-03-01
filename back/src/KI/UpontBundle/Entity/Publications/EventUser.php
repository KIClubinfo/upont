<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class EventUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Publications\Event")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $user;

    /**
     * Date du shotgun (timestamp)
     * @ORM\Column(name="shotgunDate", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Texte de motivation
     * @ORM\Column(name="motivation", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $motivation;


    //===== GENERATED AUTOMATICALLY =====//mm

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
     * Set event
     *
     * @param \KI\UpontBundle\Entity\Event $event
     * @return EventUser
     */
    public function setEvent(\KI\UpontBundle\Entity\Publications\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \KI\UpontBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return EventUser
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UpontBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return EventUser
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
     * Set motivation
     *
     * @param string $motivation
     * @return EventUser
     */
    public function setMotivation($motivation)
    {
        $this->motivation = $motivation;

        return $this;
    }

    /**
     * Get motivation
     *
     * @return string
     */
    public function getMotivation()
    {
        return $this->motivation;
    }
}
