<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Post extends Likeable
{
    /**
     * Auteur réel
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorUser;
    protected $autoSetUser = 'authorUser';
    public function getAutoSetUser() { return $this->autoSetUser; }

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Corps du texte
     * @ORM\Column(name="text", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    public function __construct()
    {
        parent::__construct();

        $this->date = time();
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Post
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
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set authorUser
     * @param \KI\UserBundle\Entity\User $authorUser
     * @return Post
     */
    public function setAuthorUser(\KI\UserBundle\Entity\User $authorUser = null)
    {
        $this->authorUser = $authorUser;

        return $this;
    }

    /**
     * Get authorUser
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
    }
}
