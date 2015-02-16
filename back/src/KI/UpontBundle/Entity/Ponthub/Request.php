<?php

namespace KI\UpontBundle\Entity\Ponthub;

use KI\UpontBundle\Entity\Ponthub\Request;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Nom de la demande d'ajout de fichier
     * @ORM\Column(name="name", type="string")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * Slug
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;
    
    /**
     * Date de la demande (timestamp)
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;
    
    /**
     * Utilisateur ayant créé la demande
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $user;
    
    /**
     * Nombre de votes
     * @ORM\Column(name="votes", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $votes;
    
    
    
    
    
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
     * Set name
     *
     * @param string $name
     * @return Request
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Request
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
     * @param integer $date
     * @return Request
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
     * Set votes
     *
     * @param integer $votes
     * @return Request
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return Request
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user = null)
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
}
