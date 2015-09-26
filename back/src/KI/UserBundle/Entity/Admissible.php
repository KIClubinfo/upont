<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity(fields={"scei"})
 */
class Admissible
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Slug
     * @Gedmo\Slug(fields={"scei"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $slug;

    /**
     * Prénom
     * @ORM\Column(name="firstName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * Nom
     * @ORM\Column(name="lastName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * Date de création de l'admissible
     * @ORM\Column(name="date", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $date;

    /**
     * Numéro SCEI
     * @ORM\Column(name="scei", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $scei;

    /**
     * Informations de contact
     * @ORM\Column(name="contact", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $contact;

    /**
     * Chambre (simple|double)
     * @ORM\Column(name="room", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $room;

    /**
     * Numéro de la série
     * @ORM\Column(name="serie", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     * @Assert\NotBlank()
     */
    protected $serie;

    /**
     * Détails
     * @ORM\Column(name="details", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $details;

    public function getName() { return $this->getFirstName().' '.$this->getLastName(); }

    public function __construct()
    {
        $this->date = time();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Admissible
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Admissible
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Admissible
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return Admissible
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set scei
     *
     * @param string $scei
     * @return Admissible
     */
    public function setScei($scei)
    {
        $this->scei = $scei;

        return $this;
    }

    /**
     * Get scei
     *
     * @return string
     */
    public function getScei()
    {
        return $this->scei;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Admissible
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set room
     *
     * @param string $room
     * @return Admissible
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set serie
     *
     * @param integer $serie
     * @return Admissible
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return integer
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Admissible
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }
}
