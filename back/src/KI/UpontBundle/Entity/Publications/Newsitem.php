<?php

namespace KI\UpontBundle\Entity\Publications;

use KI\UpontBundle\Entity\Publications\Post;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Newsitem extends Post
{
}
