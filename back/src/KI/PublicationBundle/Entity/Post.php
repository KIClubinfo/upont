<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Post extends Likeable
{
    /**
     * Au nom de quel club a été publié l'event, null si aucun club
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\Club", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorClub;

    /**
     * Auteur réel
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorUser;
    protected $autoSetUser = 'authorUser';
    public function getAutoSetUser() { return $this->autoSetUser; }

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Corps du texte
     * @ORM\Column(name="text", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * Image personnalisée
     * @ORM\OneToOne(targetEntity="KI\CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Délivre l'url de l'image du post par défaut :
     * - L'url de l'image du club si l'image du post est null,
     * - L'image de l'auteur si les deux premières sont null,
     * - null si les trois images sont null
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
    }

    public function __construct()
    {
        $this->date = time();
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Newsitem
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
     * Set text
     *
     * @param string $text
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set authorClub
     *
     * @param \KI\UserBundle\Entity\Club $authorClub
     * @return Newsitem
     */
    public function setAuthorClub(\KI\UserBundle\Entity\Club $authorClub = null)
    {
        $this->authorClub = $authorClub;

        return $this;
    }

    /**
     * Get authorClub
     *
     * @return \KI\UserBundle\Entity\Club
     */
    public function getAuthorClub()
    {
        return $this->authorClub;
    }

    /**
     * Set authorUser
     *
     * @param \KI\UserBundle\Entity\User $authorUser
     * @return Newsitem
     */
    public function setAuthorUser(\KI\UserBundle\Entity\User $authorUser = null)
    {
        $this->authorUser = $authorUser;

        return $this;
    }

    /**
     * Get authorUser
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
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
