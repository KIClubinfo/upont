<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stocke les notifications utilisateur
 * @ORM\Entity
 * @ExclusionPolicy("all")
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
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;
    
    /**
     * Titre
     * @ORM\Column(name="title", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $title;
    
    /**
     * Message
     * @ORM\Column(name="message", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $message;
    
    /**
     * Ressource : lien éventuel vers l'objet de la notification
     * @ORM\Column(name="resource", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $resource;
    
    /**
     * Mode d'envoi (to|exclude)
     * @ORM\Column(name="mode", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $mode;
    
    /**
     * Destinataire(s) ou liste d'exclusion suivant le mode d'envoi choisi
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="notifications_recipient",
     *      joinColumns={@ORM\JoinColumn(name="notifications_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    protected $recipient;

    /**
     * Personnes ayant lu la notification
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="notifications_read",
     *      joinColumns={@ORM\JoinColumn(name="notification_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *  )
     */
    protected $read;
    
    
    
    
    
    
    
    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct($title, $message, $mode = 'to', $resource = '')
    {
        $this->recipient = new \Doctrine\Common\Collections\ArrayCollection();
        $this->read = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->setTitle($title);
        $this->setMessage($message);
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
     * @param \KI\UpontBundle\Entity\User $recipient
     * @return Notification
     */
    public function addRecipient(\KI\UpontBundle\Entity\Users\User $recipient)
    {
        $this->recipient[] = $recipient;

        return $this;
    }

    /**
     * Remove recipient
     *
     * @param \KI\UpontBundle\Entity\User $recipient
     */
    public function removeRecipient(\KI\UpontBundle\Entity\Users\User $recipient)
    {
        $this->recipient->removeElement($recipient);
    }

    /**
     * Get recipient
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Add read
     *
     * @param \KI\UpontBundle\Entity\User $read
     * @return Notification
     */
    public function addRead(\KI\UpontBundle\Entity\Users\User $read)
    {
        $this->read[] = $read;

        return $this;
    }

    /**
     * Remove read
     *
     * @param \KI\UpontBundle\Entity\User $read
     */
    public function removeRead(\KI\UpontBundle\Entity\Users\User $read)
    {
        $this->read->removeElement($read);
    }

    /**
     * Get read
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRead()
    {
        return $this->read;
    }
}
