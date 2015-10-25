<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Pontlyvalent
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Texte de commentaire
     * @ORM\Column(name="text", type="text", nullable=false)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * Date
     * @ORM\Column(name="date", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $author;

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
     * Set target
     *
     * @param \KI\UserBundle\Entity\Club $target
     * @return Pontlyvalent
     */
    public function setTarget(\KI\UserBundle\Entity\User $target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set author
     *
     * @param \KI\UserBundle\Entity\User $author
     * @return Pontlyvalent
     */
    public function setAuthor(\KI\UserBundle\Entity\User $author)
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
