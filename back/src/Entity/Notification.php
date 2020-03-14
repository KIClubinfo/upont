<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stocke les notifications utilisateur
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Date
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Raison d'envoi (string unique)
     * @ORM\Column(name="reason", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $reason;

    /**
     * Titre
     * @ORM\Column(name="title", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $title;

    /**
     * Message
     * @ORM\Column(name="message", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $message;

    /**
     * Ressource : lien éventuel vers l'objet de la notification
     * @ORM\Column(name="resource", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $resource;

    /**
     * Mode d'envoi (to|exclude)
     * @ORM\Column(name="mode", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $mode;

    /**
     * Destinataire(s) ou liste d'exclusion suivant le mode d'envoi choisi
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinTable(name="notifications_recipient",
     *      joinColumns={@ORM\JoinColumn(name="notifications_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $recipients;

    /**
     * Personnes ayant lu la notification
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="notificationsRead")
     * @ORM\JoinTable(name="notifications_read",
     *      joinColumns={@ORM\JoinColumn(name="notification_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")}
     *  )
     */
    protected $reads;







    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct($reason, $title, $message, $mode = 'to', $resource = '')
    {
        $this->recipients = new ArrayCollection();
        $this->reads = new ArrayCollection();

        $this->setReason($reason);
        $this->setTitle(strip_tags($title));
        $this->setMessage(strip_tags($message));
        $this->setDate(time());
        $this->setMode($mode);
        $this->setResource($resource);
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
     * Set date
     *
     * @param integer $date
     * @return Newsitem
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
     * Set reason
     *
     * @param string $reason
     * @return Notification
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Notification
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set resource
     *
     * @param string $resource
     * @return Notification
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set mode
     *
     * @param string $mode
     * @return Notification
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Add recipient
     *
     * @param \App\Entity\User $recipient
     * @return Notification
     */
    public function addRecipient(\App\Entity\User $recipient)
    {
        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * Remove recipient
     *
     * @param \App\Entity\User $recipient
     */
    public function removeRecipient(\App\Entity\User $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    /**
     * Get recipient
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Add read
     *
     * @param \App\Entity\User $read
     * @return Notification
     */
    public function addRead(\App\Entity\User $read)
    {
        $this->reads[] = $read;

        return $this;
    }

    /**
     * Remove read
     *
     * @param \App\Entity\User $read
     */
    public function removeRead(\App\Entity\User $read)
    {
        $this->reads->removeElement($read);
    }

    /**
     * Get read
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReads()
    {
        return $this->reads;
    }
}
