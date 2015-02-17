<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Classe générique pouvant être likée/commentée
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ExclusionPolicy("all")
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
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * Slug
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
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
