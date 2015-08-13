<?php

namespace KI\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
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
     * @ORM\ManyToMany(targetEntity="KI\UserBundle\Entity\User", mappedBy="groups")
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
     * @param \KI\UserBundle\Entity\User $user
     * @return Comment
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
     * Get user
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
