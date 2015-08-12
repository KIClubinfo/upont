<?php

namespace KI\DvpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Basket extends Likeable
{
    /**
     * Contenu
     * @ORM\Column(name="content", type="string", nullable=false)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $content;

    /**
     * Prix du panier
     * @ORM\Column(name="price", type="string", nullable=false)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $price;





    //===== GENERATED AUTOMATICALLY =====//

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
