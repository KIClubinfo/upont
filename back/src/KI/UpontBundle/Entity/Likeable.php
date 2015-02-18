<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Classe générique pouvant être likée/commentée
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @JMS\ExclusionPolicy("all")
 */
class Likeable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Nom apparent
     * @ORM\Column(name="name", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * Slug
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $slug;

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
     * @JMS\VirtualProperty()
     */
    public function likes()
    {
        return count($this->listLikes);
    }

    /**
     * Ceux qui dislikent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="post_dislikes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
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

    /**
     * Les commentaires
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Comment", cascade={"persist"})
     */
    protected $comments;
















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
     * Set path
     *
     * @param string $path
     * @return Album
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Album
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
     * Set slug
     *
     * @param string $slug
     * @return Album
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
     * Add dislike
     *
     * @param \KI\UpontBundle\Entity\User $dislikes
     * @return PonthubFile
     */
    public function addDislike(\KI\UpontBundle\Entity\Users\User $dislike)
    {
        $this->listDislikes[] = $dislike;

        return $this;
    }

    /**
     * Remove dislikes
     *
     * @param \KI\UpontBundle\Entity\User $dislikes
     */
    public function removeDislike(\KI\UpontBundle\Entity\Users\User $dislike)
    {
        $this->listDislikes->removeElement($dislike);
    }

    /**
     * Get dislikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDislikes()
    {
        return $this->listDislikes;
    }

    /**
     * Set dislikes
     *
     * @return PonthubFile
     */
    public function setDislikes($dislikes)
    {
        return $this->listDislikes = $dislikes;
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

    /**
     * Add comment
     *
     * @param \KI\UpontBundle\Entity\Comment $comments
     * @return PonthubFile
     */
    public function addcomment(\KI\UpontBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \KI\UpontBundle\Entity\Comment $comments
     */
    public function removeComment(\KI\UpontBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set comments
     *
     * @return PonthubFile
     */
    public function setComments($comments)
    {
        return $this->comments = $comments;
    }
}
