<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stocke un commentaire utilisateur
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Date
     * @ORM\Column(name="date", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Le commentaire en lui même
     * @ORM\Column(name="text", type="text")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * Auteur
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @Expose
     */
    protected $author;

    /**
     * Ceux qui likent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="comment_likes",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
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
     * @ORM\JoinTable(name="comment_unlikes",
     *      joinColumns={@ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
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
     * Constructor
     */
    public function __construct()
    {
        $this->listLikes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->listUnlikes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \KI\UpontBundle\Entity\Users\User $author
     * @return Comment
     */
    public function setAuthor(\KI\UpontBundle\Entity\Users\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \KI\UpontBundle\Entity\Users\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add listLikes
     *
     * @param \KI\UpontBundle\Entity\Users\User $listLikes
     * @return Comment
     */
    public function addLike(\KI\UpontBundle\Entity\Users\User $listLikes)
    {
        $this->listLikes[] = $listLikes;

        return $this;
    }

    /**
     * Remove listLikes
     *
     * @param \KI\UpontBundle\Entity\Users\User $listLikes
     */
    public function removeLike(\KI\UpontBundle\Entity\Users\User $listLikes)
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
     * Add listUnlikes
     *
     * @param \KI\UpontBundle\Entity\Users\User $listUnlikes
     * @return Comment
     */
    public function addUnlike(\KI\UpontBundle\Entity\Users\User $listUnlikes)
    {
        $this->listUnlikes[] = $listUnlikes;

        return $this;
    }

    /**
     * Remove listUnlikes
     *
     * @param \KI\UpontBundle\Entity\Users\User $listUnlikes
     */
    public function removeUnlike(\KI\UpontBundle\Entity\Users\User $listUnlikes)
    {
        $this->listUnlikes->removeElement($listUnlikes);
    }

    /**
     * Get listUnlikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnlikes()
    {
        return $this->listUnlikes;
    }
}
