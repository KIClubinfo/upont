<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsitemRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Newsitem extends Post
{
    /**
     * @var \App\Entity\Image
     *
     * Image personnalisée
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
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
        if ($this->name === "message") {
            if ($this->image !== null) {
                return $this->image->getWebPath();
            } else
                return '';
        } else {
            if ($this->authorClub !== null && $this->authorClub->getImage() !== null) {
                return $this->authorClub->getImage()->getWebPath();
            } else
                return null;
        }
    }

    /**
     * Set image
     *
     * @param \App\Entity\Image $image
     * @return Newsitem
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
