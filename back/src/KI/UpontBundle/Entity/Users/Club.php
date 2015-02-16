<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Club
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Nom court, abbréviation
     * @ORM\Column(name="shortName", type="string", length=20)
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $shortName;

    /**
     * Nom complet
     * @ORM\Column(name="name", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $name;

    /**
     * Logo
     * @ORM\OneToOne(targetEntity="KI\UpontBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Icône (utilisée par l'application mobile)
     * @ORM\Column(name="icon", type="string", nullable=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $icon;

    /**
     * Slug
     * @Gedmo\Slug(fields={"shortName"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;

    /**
     * Club actif ou non ?
     * @ORM\Column(name="active", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $active;

    /**
     * @VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : 'uploads/images/default-user.png';
    }







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
     * Set shortName
     *
     * @param string $shortName
     * @return Club
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Club
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
     * Set icon
     *
     * @param string $icon
     * @return Club
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Club
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
     * Set active
     *
     * @param boolean $active
     * @return Club
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set image
     *
     * @param \KI\UpontBundle\Entity\Image $image
     * @return Club
     */
    public function setImage(\KI\UpontBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \KI\UpontBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
