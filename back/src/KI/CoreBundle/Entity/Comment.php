<?php

namespace KI\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stocke un commentaire utilisateur
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * Date
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Le commentaire en lui même
     * @ORM\Column(name="text", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * Auteur
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     */
    protected $author;

    /**
     * Ceux qui likent
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="comment_likes",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="liker_id", referencedColumnName="id")}
     * )
     */
    protected $listLikes;

    /**
     * Nombre de ceux qui likent
     * @JMS\VirtualProperty()
     */
    public function likes()
    {
        return count($this->listLikes);
    }

    /**
     * Ceux qui dislikent
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="comment_dislikes",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="disliker_id", referencedColumnName="id")}
     *  )
     */
    protected $listDislikes;

    /**
     * Nombre de ceux qui dislikent
     * @JMS\VirtualProperty()
     */
    public function dislikes()
    {
        return count($this->listDislikes);
    }

    /**
     * @JMS\Expose
     */
    protected $like = false;

    /**
     * @JMS\Expose
     */
    protected $dislike = false;







    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listLikes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->listDislikes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Comment
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
     * @return Comment
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
     * Set author
     *
     * @param \KI\UserBundle\Entity\User $author
     * @return Comment
     */
    public function setAuthor(\KI\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add listLikes
     *
     * @param \KI\UserBundle\Entity\User $listLikes
     * @return Comment
     */
    public function addLike(\KI\UserBundle\Entity\User $listLikes)
    {
        $this->listLikes[] = $listLikes;

        return $this;
    }

    /**
     * Remove listLikes
     *
     * @param \KI\UserBundle\Entity\User $listLikes
     */
    public function removeLike(\KI\UserBundle\Entity\User $listLikes)
    {
        $this->listLikes->removeElement($listLikes);
    }

    /**
     * Get listLikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->listLikes;
    }

    /**
     * Add listDislikes
     *
     * @param \KI\UserBundle\Entity\User $listDislikes
     * @return Comment
     */
    public function addDislike(\KI\UserBundle\Entity\User $listDislikes)
    {
        $this->listDislikes[] = $listDislikes;

        return $this;
    }

    /**
     * Remove listDislikes
     *
     * @param \KI\UserBundle\Entity\User $listDislikes
     */
    public function removeDislike(\KI\UserBundle\Entity\User $listDislikes)
    {
        $this->listDislikes->removeElement($listDislikes);
    }

    /**
     * Get listDislikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDislikes()
    {
        return $this->listDislikes;
    }

    public function getDislike()
    {
        return $this->dislike;
    }

    public function setDislike($dislike)
    {
        return $this->dislike = $dislike;
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
