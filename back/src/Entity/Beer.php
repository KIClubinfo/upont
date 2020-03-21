<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BeerRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Beer extends Likeable
{
    /**
     * Prix
     * @ORM\Column(name="price", type="float")
     * @JMS\Expose
     * @Assert\Type("numeric")
     */
    protected $price;

    /**
     * Taux d'alcohol
     * @ORM\Column(name="ects", type="float")
     * @JMS\Expose
     * @Assert\Type("numeric")
     */
    protected $alcohol;

    /**
     * Volume (L)
     * @ORM\Column(name="volume", type="float")
     * @JMS\Expose
     * @Assert\Type("numeric")
     */
    protected $volume;

    /**
     * Nombre en stock
     * @ORM\Column(name="stock", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $stock = 0;

    /**
     * Statut de la bière pour l'affichage
     * @ORM\Column(name="active", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $active = true;

    /**
     * Transactions liées à la bière
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="beer")
     * @Assert\Valid()
     */
    protected $transactions;

    /**
     * Logo
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : null;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Beer
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set alcohol
     *
     * @param float $alcohol
     *
     * @return Beer
     */
    public function setAlcohol($alcohol)
    {
        $this->alcohol = $alcohol;

        return $this;
    }

    /**
     * Get alcohol
     *
     * @return float
     */
    public function getAlcohol()
    {
        return $this->alcohol;
    }

    /**
     * Set volume
     *
     * @param float $volume
     *
     * @return Beer
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume
     *
     * @return float
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Beer
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

        /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Beer
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
     * @param \App\Entity\Image $image
     *
     * @return Beer
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
}
