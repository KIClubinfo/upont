<?php

namespace KI\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Classe générique pouvant être likée/commentée
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @JMS\ExclusionPolicy("all")
 */
class Likeable extends LikeClass
{
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
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="post_likes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="liker_id", referencedColumnName="id")}
     * )
     */
    protected $listLikes;

    /**
     * Ceux qui dislikent
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="post_dislikes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="disliker_id", referencedColumnName="id")}
     *  )
     */
    protected $listDislikes;

    /**
     * Les commentaires
     * @ORM\ManyToMany(targetEntity="KI\CoreBundle\Entity\Comment", cascade={"persist", "remove"})
     */
    protected $listComments;

    /**
     * Nombre de commentaires
     * @JMS\VirtualProperty()
     */
    public function comments()
    {
        return count($this->listComments);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->listComments = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Likeable
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
     * @return Likeable
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
     * Add comment
     *
     * @param \KI\CoreBundle\Entity\Comment $comment
     * @return Likeable
     */
    public function addcomment(\KI\CoreBundle\Entity\Comment $comment)
    {
        $this->listComments[] = $comment;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \KI\CoreBundle\Entity\Comment $comment
     */
    public function removeComment(\KI\CoreBundle\Entity\Comment $comment)
    {
        $this->listComments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->listComments;
    }

    /**
     * Set comments
     *
     * @return Likeable
     */
    public function setComments($comments)
    {
        return $this->listComments = $comments;
    }
}
