<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Message extends Post
{
    /**
     * @var \KI\CoreBundle\Entity\Image
     *
     * Image personnalisée
     * @ORM\OneToOne(targetEntity="KI\CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Délivre l'url de l'image du post
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->image !== null)
        {
            return $this->image->getWebPath();
        }
        else
            return '';
    }

    /**
     * Set image
     *
     * @param \KI\CoreBundle\Entity\Image $image
     * @return Message
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

    public function __construct(){
        parent::__construct();

        $this->setName("message");
    }
}
