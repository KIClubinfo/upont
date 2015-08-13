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
class Comment extends LikeClass
{
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
    public $autoSetUser = 'author';
    public function getAutoSetUser() { return $this->autoSetUser; }

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
     * Ceux qui dislikent
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="comment_dislikes",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="disliker_id", referencedColumnName="id")}
     *  )
     */
    protected $listDislikes;

    public function __construct()
    {
        parent::__construct();
        $this->date = time();
    }

    public function getSlug()
    {
        return $this->getId();
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
}
