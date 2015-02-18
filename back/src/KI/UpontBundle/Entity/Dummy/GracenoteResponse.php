<?php

namespace KI\UpontBundle\Entity\Dummy;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
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
     * @JMS\Expose
     */
    protected $name;

    /**
     * Artiste
     * @ORM\Column(name="artist", type="string")
     * @JMS\Expose
     */
    protected $artist;

    /**
     * Année
     * @ORM\Column(name="year", type="string")
     * @JMS\Expose
     */
    protected $year;

    /**
     * URL de la couverture d'album
     * @ORM\Column(name="image", type="string")
     * @JMS\Expose
     */
    protected $image;
}
