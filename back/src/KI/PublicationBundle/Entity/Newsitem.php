<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Newsitem extends Post
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
     * Délivre l'url de :
     * - l'image du post
     * - l'image du club sinon
     * - l'image d'utilisateur par défaut sinon
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->image !== null) {
            return $this->image->getWebPath();
        }
        else if ($this->authorClub !== null && $this->authorClub->getImage() !== null) {
            return $this->authorClub->getImage()->getWebPath();
        }

        return 'uploads/others/default-user.png';
    }

    /**
     * Set image
     *
     * @param \KI\CoreBundle\Entity\Image $image
     * @return Newsitem
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
