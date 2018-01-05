<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Groupe d'Users utilisé par le FOSUserBundle pour déterminer les permissions
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 * @JMS\ExclusionPolicy("all")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Utilisateurs faisant partie de ce groupe
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="groups")
     **/
    protected $users;

    /**
     * Slug
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $slug;

    /**
     * Constructor
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Group
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
     * Add user
     * // Never use this setter directly
     * // use the User's addGroupUser($group) (which links to this one)
     *
     * @param \App\Entity\User $user
     * @return Group
     */
    public function addUser(\App\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\User $user
     */
    public function removeUser(\App\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get user
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
