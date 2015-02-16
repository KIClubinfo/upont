<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ExclusionPolicy("all")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Au nom de quel club a été publié l'event, null si aucun club
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\Club", cascade={"persist"})
     * @Expose
     * @Assert\Valid()
     */
    protected $authorClub;

    /**
     * Auteur réel
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @Expose
     * @Assert\Valid()
     */
    protected $authorUser;

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Titre
     * @ORM\Column(name="title", type="string", length=100)
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * Texte d'accroche
     * @ORM\Column(name="textShort", type="string", length=150, nullable=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $textShort;

    /**
     * Corps du texte
     * @ORM\Column(name="textLong", type="text")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $textLong;

    /**
     * Slug
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;

    /**
     * Image personnalisée
     * @ORM\OneToOne(targetEntity="KI\UpontBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Délivre l'url de l'image du post par défaut, l'url de l'image du club si l'image du post est null, l'image de l'auteur si les deux premières sont null, et null si les trois images sont null
     * @VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->image !== null) return $this->image->getWebPath();
        else if ($this->authorClub !== null && $this->authorClub->getImage() !== null) return $this->authorClub->getImage()->getWebPath();
        else if ($this->authorUser !== null && $this->authorUser->getImage() !== null) return $this->authorUser->getImage()->getWebPath();
        return 'uploads/images/default-user.png';
    }

    /**
     * Ceux qui likent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="post_likes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="liker_id", referencedColumnName="id")}
     * )
     */
    protected $listLikes;

    /**
     * Nombre de ceux qui likent
     * @VirtualProperty()
     */
    public function likes()
    {
        return count($this->listLikes);
    }

    /**
     * Ceux qui unlikent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="post_unlikes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="unliker_id", referencedColumnName="id")}
     *  )
     */
    protected $listUnlikes;

    /**
     * Nombre de ceux qui unlikent
     * @VirtualProperty()
     */
    public function unlikes()
    {
        return count($this->listUnlikes);
    }

    /**
     * @Expose
     */
    protected $like = false;

    /**
     * @Expose
     */
    protected $unlike = false;







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
     * Set title
     *
     * @param string $title
     * @return Newsitem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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

    /**
     * Add like
     *
     * @param \KI\UpontBundle\Entity\User $likes
     * @return PonthubFile
     */
    public function addLike(\KI\UpontBundle\Entity\Users\User $like)
    {
        $this->listLikes[] = $like;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \KI\UpontBundle\Entity\User $likes
     */
    public function removeLike(\KI\UpontBundle\Entity\Users\User $like)
    {
        $this->listLikes->removeElement($like);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->listLikes;
    }

    /**
     * Set likes
     *
     * @return PonthubFile
     */
    public function setLikes($likes)
    {
        return $this->listLikes = $likes;
    }

    /**
     * Add unlike
     *
     * @param \KI\UpontBundle\Entity\User $unlikes
     * @return PonthubFile
     */
    public function addUnlike(\KI\UpontBundle\Entity\Users\User $unlike)
    {
        $this->listUnlikes[] = $unlike;

        return $this;
    }

    /**
     * Remove unlikes
     *
     * @param \KI\UpontBundle\Entity\User $unlikes
     */
    public function removeUnlike(\KI\UpontBundle\Entity\Users\User $unlike)
    {
        $this->listUnlikes->removeElement($unlike);
    }

    /**
     * Get unlikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnlikes()
    {
        return $this->listUnlikes;
    }

    /**
     * Set unlikes
     *
     * @return PonthubFile
     */
    public function setUnlikes($unlikes)
    {
        return $this->listUnlikes = $unlikes;
    }

    public function getUnlike()
    {
        return $this->unlike;
    }

    public function setUnlike($unlike)
    {
        return $this->unlike = $unlike;
    }

    public function getLike()
    {
        return $this->like;
    }

    public function setLike($like)
    {
        return $this->like = $like;
    }
}
