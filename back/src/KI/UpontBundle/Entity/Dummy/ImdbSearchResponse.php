<?php

namespace KI\UpontBundle\Entity\Dummy;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class ImdbSearchResponse
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ids", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ids;

    /**
     * Nom du film/de la série
     * @ORM\Column(name="name", type="string")
     * @JMS\Expose
     */
    protected $name;

    /**
     * Type (movie|series)
     * @ORM\Column(name="type", type="string")
     * @JMS\Expose
     */
    protected $type;

    /**
     * Année
     * @ORM\Column(name="year", type="string")
     * @JMS\Expose
     */
    protected $year;

    /**
     * Identifiant Imdb
     * @ORM\Column(name="id", type="string")
     * @JMS\Expose
     */
    protected $id;
}
