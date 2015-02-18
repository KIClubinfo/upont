<?php

namespace KI\UpontBundle\Entity\Users;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

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
}
