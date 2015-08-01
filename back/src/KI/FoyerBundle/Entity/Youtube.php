<?php

namespace KI\FoyerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * Représentation d'un lien YouTube pour les suggestions de musique du Foyer
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Youtube extends Likeable
{
    /**
     * Lien/suggestion
     * @ORM\Column(name="link", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $link;

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Auteur
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $user;

    /**
     * On n'oublie pas d'ajouter le HTTPS s'il n'est pas présent
     * @JMS\VirtualProperty()
     */
    public function url()
    {
        $link = $this->link;

        if (preg_match('#^www#', $link))
            $link = 'https://'.$link;

        return $link;
    }








    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set link
     *
     * @param string $link
     * @return Youtube
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Youtube
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
     * Set user
     *
     * @param \KI\UserBundle\Entity\User $user
     * @return Youtube
     */
    public function setUser(\KI\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
