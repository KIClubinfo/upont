<?php

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventUserRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $user;

    /**
     * Date du shotgun
     * @ORM\Column(name="shotgunDate", type="carbondatetime")
     * @JMS\Expose
     * @Assert\DateTime()
     */
    protected $date;

    /**
     * Texte de motivation
     * @ORM\Column(name="motivation", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $motivation;

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
     * @param Event $event
     * @return EventUser
     */
    public function setEvent(Event $event): EventUser
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return EventUser
     */
    public function setUser(User $user): EventUser
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
     * Set date
     *
     * @param Carbon $date
     * @return EventUser
     */
    public function setDate(Carbon $date): EventUser
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return Carbon
     */
    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    /**
     * Set motivation
     *
     * @param string $motivation
     * @return EventUser
     */
    public function setMotivation($motivation): EventUser
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
