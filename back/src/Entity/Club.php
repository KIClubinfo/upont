<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Club extends Likeable
{
    /**
     * Nom complet du club
     * @ORM\Column(name="fullName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $fullName;

    /**
     * Logo
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Bannière
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $banner;

    /**
     * Icône (utilisée par l'application mobile)
     * @ORM\Column(name="icon", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $icon;

    /**
     * Corps du texte
     * @ORM\Column(name="presentation", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $presentation;

    /**
     * Club actif ou non ?
     * @ORM\Column(name="active", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $active;

    /**
     * asso | club-gastronomique/artistique/divertissement/culturel | autre
     * @ORM\Column(name="category", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $category;

    /**
     * Channel géré par l'administration ?
     * @ORM\Column(name="administration", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $administration;

    /**
     * Adresse du local
     * @ORM\Column(name="place", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $place;

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : null;
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function bannerUrl()
    {
        return $this->banner !== null ? $this->banner->getWebPath() : null;
    }







    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return Club
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
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
     * Set presentation
     *
     * @param text $presentation
     * @return Club
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return text
     */
    public function getPresentation()
    {
        return $this->presentation;
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
     * Set category
     *
     * @param boolean $category
     * @return Club
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get administration
     *
     * @return boolean
     */
    public function getAdministration()
    {
        return $this->administration;
    }

    /**
     * Set administration
     *
     * @param boolean $administration
     * @return Club
     */
    public function setAdministration($administration)
    {
        $this->administration = $administration;

        return $this;
    }

    /**
     * Set image
     *
     * @param \App\Entity\Image $image
     * @return Club
     */
    public function setImage(\App\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \App\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set banner
     *
     * @param \App\Entity\Image $banner
     * @return Club
     */
    public function setBanner(\App\Entity\Image $banner = null)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return \App\Entity\Image
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Club
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
}
