<?php

namespace KI\DvpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Basket extends Likeable
{
    /**
     * Contenu
     * @ORM\Column(name="content", type="text", nullable=false)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $content;

    /**
     * Prix du panier
     * @ORM\Column(name="price", type="float", nullable=false)
     * @JMS\Expose
     * @Assert\Type("numeric")
     * @Assert\NotBlank()
     */
    protected $price;

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Basket
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Basket
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
}
