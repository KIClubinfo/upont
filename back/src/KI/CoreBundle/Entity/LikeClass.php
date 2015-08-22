<?php

namespace KI\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\UserBundle\Entity\User;

/**
 * Propriétés de base d'une entité qui se like
 * @JMS\ExclusionPolicy("all")
 */
class LikeClass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

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

    public function isLiked(User $user)
    {
        return $this->listLikes->contains($user);
    }

    /**
     * @JMS\Expose
     */
    protected $dislike = false;

    public function isDisliked(User $user)
    {
        return $this->listDislikes->contains($user);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listLikes    = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add listLikes
     *
     * @param \KI\UserBundle\Entity\User $listLikes
     * @return LikeClass
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
     * Set listLikes
     *
     * @param \KI\UserBundle\Entity\User $listLikes
     * @return LikeClass
     */
    public function setLikes($listLikes)
    {
        return $this->listLikes = $listLikes;
    }

    /**
     * Add listDislikes
     *
     * @param \KI\UserBundle\Entity\User $listDislikes
     * @return LikeClass
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

    /**
     * Set listDislikes
     *
     * @param \KI\UserBundle\Entity\User $listDislikes
     * @return LikeClass
     */
    public function setDislikes($listDislikes)
    {
        return $this->listDislikes = $listDislikes;
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
