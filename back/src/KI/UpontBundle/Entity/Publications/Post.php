<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Core\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Post extends Likeable
{
    /**
     * Au nom de quel club a été publié l'event, null si aucun club
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\Club", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorClub;

    /**
     * Auteur réel
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorUser;

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Texte d'accroche
     * @ORM\Column(name="textShort", type="string", length=150, nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $textShort;

    /**
     * Corps du texte
     * @ORM\Column(name="textLong", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $textLong;

    /**
     * Image personnalisée
     * @ORM\OneToOne(targetEntity="KI\UpontBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Délivre l'url de l'image du post par défaut, l'url de l'image du club si l'image du post est null, l'image de l'auteur si les deux premières sont null, et null si les trois images sont null
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->image !== null) return $this->image->getWebPath();
        else if ($this->authorClub !== null && $this->authorClub->getImage() !== null) return $this->authorClub->getImage()->getWebPath();
        else if ($this->authorUser !== null && $this->authorUser->getImage() !== null) return $this->authorUser->getImage()->getWebPath();
        return 'uploads/others/default-user.png';
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
     * Set name
     *
     * @param string $name
     * @return Newsitem
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
     * Set textShort
     *
     * @param string $textShort
     * @return Newsitem
     */
    public function setTextShort($textShort)
    {
        $this->textShort = $textShort;

        return $this;
    }

    /**
     * Get textShort
     *
     * @return string
     */
    public function getTextShort()
    {
        return $this->textShort;
    }

    /**
     * Set textLong
     *
     * @param string $textLong
     * @return Newsitem
     */
    public function setTextLong($textLong)
    {
        $this->textLong = $textLong;

        return $this;
    }

    /**
     * Get textLong
     *
     * @return string
     */
    public function getTextLong()
    {
        return $this->textLong;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Newsitem
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
     * Set authorClub
     *
     * @param \KI\UpontBundle\Entity\Club $authorClub
     * @return Newsitem
     */
    public function setAuthorClub(\KI\UpontBundle\Entity\Users\Club $authorClub = null)
    {
        $this->authorClub = $authorClub;

        return $this;
    }

    /**
     * Get authorClub
     *
     * @return \KI\UpontBundle\Entity\Club
     */
    public function getAuthorClub()
    {
        return $this->authorClub;
    }

    /**
     * Set authorUser
     *
     * @param \KI\UpontBundle\Entity\User $authorUser
     * @return Newsitem
     */
    public function setAuthorUser(\KI\UpontBundle\Entity\Users\User $authorUser = null)
    {
        $this->authorUser = $authorUser;

        return $this;
    }

    /**
     * Get authorUser
     *
     * @return \KI\UpontBundle\Entity\User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
    }

    /**
     * Set image
     *
     * @param \KI\UpontBundle\Entity\Image $image
     * @return Newsitem
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
