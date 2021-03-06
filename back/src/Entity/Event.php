<?php

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Event extends Post
{
    /**
     * Début
     * @ORM\Column(name="startDate", type="integer")
     * @Assert\Type("int")
     */
    protected $startDate;

    /**
     * Fin
     * @ORM\Column(name="endDate", type="integer")
     * @Assert\Type("int")
     */
    protected $endDate;

    /**
     * Mode d'entrée [libre|shotgun|ferie]
     * @ORM\Column(name="entryMethod", type="string", length=255)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $entryMethod;
    const TYPE_LIBRE = 'Libre';
    const TYPE_SHOTGUN = 'Shotgun';
    const TYPE_FERIE = 'Ferie';

    /**
     * Date du shotgun
     * @ORM\Column(name="shotgunDate", type="integer", nullable=true)
     * @Assert\Type("int")
     */
    protected $shotgunDate;

    /**
     * Nombre de places limite
     * @ORM\Column(name="shotgunLimit", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     */
    protected $shotgunLimit;

    /**
     * Texte dévoilé à ceux qui ont réussi le shotgun
     * @ORM\Column(name="shotgunText", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $shotgunText;

    /**
     * Lieu
     * @ORM\Column(name="place", type="string", length=255)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $place;

    /**
     * Participants
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="event_attendee",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attendee_id", referencedColumnName="id")}
     * )
     */
    protected $listAttendees;

    /**
     * @JMS\Expose
     */
    protected $attend = false;

    public function isAttended(User $user)
    {
        return $this->listAttendees->contains($user);
    }

    /**
     * @JMS\Expose
     */
    protected $pookie = false;

    public function isHidden(User $user)
    {
        return $this->listPookies->contains($user);
    }

    /**
     * Nombre de ceux qui participent
     * @JMS\VirtualProperty()
     */
    public function attendees()
    {
        return count($this->listAttendees);
    }

    /**
     * Personnes ayant refusées
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="event_pookie",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="pooky_id", referencedColumnName="id")}
     *  )
     */
    protected $listPookies;

    /**
     * Nombre de ceux qui sont des pookies
     * @JMS\VirtualProperty()
     */
    public function pookies()
    {
        return count($this->listPookies);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->listAttendees = new ArrayCollection();
        $this->listPookies = new ArrayCollection();
    }

    public function setStartDate(Carbon $startDate): Event
    {
        $this->startDate = $startDate->getTimestamp();

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function getStartDate(): ?Carbon
    {
        return Carbon::createFromTimestamp($this->startDate);
    }

    public function setEndDate(Carbon $endDate): Event
    {
        $this->endDate = $endDate->getTimestamp();

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function getEndDate(): ?Carbon
    {
        return Carbon::createFromTimestamp($this->endDate);
    }

    /**
     * Set entryMethod
     *
     * @param string $entryMethod
     * @return Event
     */
    public function setEntryMethod($entryMethod)
    {
        $this->entryMethod = $entryMethod;

        return $this;
    }

    public function getEntryMethod()
    {
        return $this->entryMethod;
    }

    public function setShotgunDate(?Carbon $shotgunDate): Event
    {
        $this->shotgunDate = $shotgunDate ? $shotgunDate->getTimestamp() : null;

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function getShotgunDate(): ?Carbon
    {
        if ($this->shotgunDate === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->shotgunDate);
    }

    /**
     * Set shotgunLimit
     *
     * @param integer $shotgunLimit
     * @return Event
     */
    public function setShotgunLimit(int $shotgunLimit)
    {
        $this->shotgunLimit = $shotgunLimit;

        return $this;
    }

    /**
     * Get shotgunLimit
     *
     * @return integer
     */
    public function getShotgunLimit()
    {
        return $this->shotgunLimit;
    }

    /**
     * Set shotgunText
     *
     * @param string $shotgunText
     * @return Event
     */
    public function setShotgunText($shotgunText)
    {
        $this->shotgunText = $shotgunText;

        return $this;
    }

    /**
     * Get shotgunText
     *
     * @return string
     */
    public function getShotgunText()
    {
        return $this->shotgunText;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Event
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Add attendees
     *
     * @param User $attendee
     * @return Event
     */
    public function addAttendee(User $attendee)
    {
        $this->listAttendees[] = $attendee;

        return $this;
    }

    /**
     * Remove attendees
     *
     * @param User $attendee
     */
    public function removeAttendee(User $attendee)
    {
        $this->listAttendees->removeElement($attendee);
    }

    /**
     * Get attendees
     *
     * @return ArrayCollection
     */
    public function getAttendees()
    {
        return $this->listAttendees;
    }

    /**
     * Add pookies
     *
     * @param User $pookie
     * @return Event
     */
    public function addPookie(User $pookie)
    {
        $this->listPookies[] = $pookie;

        return $this;
    }

    /**
     * Remove pookies
     *
     * @param User $pookie
     */
    public function removePookie(User $pookie)
    {
        $this->listPookies->removeElement($pookie);
    }

    /**
     * Get pookies
     *
     * @return ArrayCollection
     */
    public function getPookies()
    {
        return $this->listPookies;
    }

    public function getAttend()
    {
        return $this->attend;
    }

    public function setAttend($attend)
    {
        return $this->attend = $attend;
    }

    public function getPookie()
    {
        return $this->pookie;
    }

    public function setPookie($pookie)
    {
        return $this->pookie = $pookie;
    }
}
