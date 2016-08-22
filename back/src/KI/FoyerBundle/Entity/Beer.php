<?php

namespace KI\FoyerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
     * Logo
     * @ORM\OneToOne(targetEntity="KI\CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

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
     * Set image
     *
     * @param \KI\CoreBundle\Entity\Image $image
     *
     * @return Beer
     */
    public function setImage(\KI\CoreBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \KI\CoreBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
