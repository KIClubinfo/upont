<?php

namespace KI\UpontBundle\Entity\Publications;

use KI\UpontBundle\Entity\Publications\Post;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use \DateTime;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Event extends Post
{
    /**
     * Début (timestamp)
     * @ORM\Column(name="startDate", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $startDate;

    /**
     * Fin (timestamp)
     * @ORM\Column(name="endDate", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $endDate;

    /**
     * Mode d'entrée [libre|shotgun]
     * @ORM\Column(name="entryMethod", type="string", length=255)
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $entryMethod;

    /**
     * Date du shotgun (timestamp)
     * @ORM\Column(name="shotgunDate", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $shotgunDate;

    /**
     * Nombre de places limite
     * @ORM\Column(name="shotgunLimit", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $shotgunLimit;

    /**
     * Texte dévoilé à ceux qui ont réussi le shotgun
     * @ORM\Column(name="shotgunText", type="text", nullable=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $shotgunText;

    /**
     * Lieu
     * @ORM\Column(name="place", type="string", length=255)
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $place;

    /**
     * Participants
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="event_attendee",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attendee_id", referencedColumnName="id")}
     * )
     */
    protected $listAttendees;

    /**
     * @Expose
     */
    protected $attend = false;

    /**
     * @Expose
     */
    protected $pookie = false;

    /**
     * Nombre de ceux qui participent
     * @VirtualProperty()
     */
    public function attendees()
    {
        return count($this->listAttendees);
    }

    /**
     * Personnes ayant refusées
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="event_pookie",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="pooky_id", referencedColumnName="id")}
     *  )
     */
    protected $listPookies;

    /**
     * Nombre de ceux qui sont des pookies
     * @VirtualProperty()
     */
    public function pookies()
    {
        return count($this->listPookies);
    }








    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listAttendees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pookies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set startDate
     *
     * @param integer $startDate
     * @return Event
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param integer $endDate
     * @return Event
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->endDate;
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

    /**
     * Get entryMethod
     *
     * @return string
     */
    public function getEntryMethod()
    {
        return $this->entryMethod;
    }

    /**
     * Set shotgunDate
     *
     * @param integer $shotgunDate
     * @return Event
     */
    public function setShotgunDate($shotgunDate)
    {
        $this->shotgunDate = $shotgunDate;

        return $this;
    }

    /**
     * Get shotgunDate
     *
     * @return integer
     */
    public function getShotgunDate()
    {
        return $this->shotgunDate;
    }

    /**
     * Set shotgunLimit
     *
     * @param integer $shotgunLimit
     * @return Event
     */
    public function setShotgunLimit($shotgunLimit)
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
     * @param \KI\UpontBundle\Entity\User $attendee
     * @return Event
     */
    public function addAttendee(\KI\UpontBundle\Entity\Users\User $attendee)
    {
        $this->listAttendees[] = $attendee;

        return $this;
    }

    /**
     * Remove attendees
     *
     * @param \KI\UpontBundle\Entity\User $attendee
     */
    public function removeAttendee(\KI\UpontBundle\Entity\Users\User $attendee)
    {
        $this->listAttendees->removeElement($attendee);
    }

    /**
     * Get attendees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttendees()
    {
        return $this->listAttendees;
    }

    /**
     * Add pookies
     *
     * @param \KI\UpontBundle\Entity\User $pookie
     * @return Event
     */
    public function addPookie(\KI\UpontBundle\Entity\Users\User $pookie)
    {
        $this->listPookies[] = $pookie;

        return $this;
    }

    /**
     * Remove pookies
     *
     * @param \KI\UpontBundle\Entity\User $pookie
     */
    public function removePookie(\KI\UpontBundle\Entity\Users\User $pookie)
    {
        $this->listPookies->removeElement($pookie);
    }

    /**
     * Get pookies
     *
     * @return \Doctrine\Common\Collections\Collection
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




    //===== CUSTOM METHODS =====//
    /**
     * Get StartDateTime
     *
     * @return \DateTime
     */
    public function getStartDateTime()
    {
        $date = new DateTime();
        $date->setTimestamp($this->getStartDate());
        return $date;
    }

    /**
     * Get EndDateTime
     *
     * @return \DateTme
     */
    public function getEndDateTime()
    {
        $date = new DateTime();
        $date->setTimestamp($this->getEndDate());
        return $date;
    }
}
