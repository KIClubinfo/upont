<?php

namespace KI\UpontBundle\Entity\Dummy;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class GracenoteResponse
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Nom de l'album
     * @ORM\Column(name="name", type="string")
     * @Expose
     */
    protected $name;
    
    /**
     * Artiste
     * @ORM\Column(name="artist", type="string")
     * @Expose
     */
    protected $artist;
    
    /**
     * Année
     * @ORM\Column(name="year", type="string")
     * @Expose
     */
    protected $year;
    
    /**
     * URL de la couverture d'album
     * @ORM\Column(name="image", type="string")
     * @Expose
     */
    protected $image;
}
