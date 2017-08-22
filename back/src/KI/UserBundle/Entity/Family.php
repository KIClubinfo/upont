<?php

namespace KI\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Family
 *
 * @ORM\Table(name="family")
 * @ORM\Entity(repositoryClass="KI\UserBundle\Repository\FamilyRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Family extends Likeable
{

    /**
     * Nom complet de la famille
     * @ORM\Column(name="fullName", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $fullName;

    /**
     * Membres de la famille
     * @ORM\OneToMany(targetEntity="KI\UserBundle\Entity\User", mappedBy="family", orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $users;

    /**
     * Logo
     * @ORM\OneToOne(targetEntity="KI\CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */

    protected $image;

    /**
     * Bannière
     * @ORM\OneToOne(targetEntity="KI\CoreBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $banner;

    /**
     * Corps du texte
     * @ORM\Column(name="presentation", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $presentation;

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : 'uploads/others/default-user.png';
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function bannerUrl()
    {
        return $this->banner !== null ? $this->banner->getWebPath() : null;
    }










    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return Club
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Add user
     *
     * @param \KI\UserBundle\Entity\User $user
     *
     * @return Family
     */
    public function addUser(\KI\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \KI\UserBundle\Entity\User $user
     */
    public function removeUser(\KI\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set presentation
     *
     * @param text $presentation
     * @return Club
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return text
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set image
     *
     * @param \KI\CoreBundle\Entity\Image $image
     * @return Club
     */
    public function setImage(\KI\CoreBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \KI\CoreBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set banner
     *
     * @param \KI\CoreBundle\Entity\Image $banner
     * @return Club
     */
    public function setBanner(\KI\CoreBundle\Entity\Image $banner = null)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return \KI\CoreBundle\Entity\Image
     */
    public function getBanner()
    {
        return $this->banner;
    }
}
