<?php

namespace KI\UpontBundle\Entity\Dummy;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class ImdbInfosResponse
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Titre
     * @ORM\Column(name="title", type="string")
     * @Expose
     */
    protected $title;
    
    /**
     * Année
     * @ORM\Column(name="year", type="string")
     * @Expose
     */
    protected $year;
    
    /**
     * Durée (en secondes)
     * @ORM\Column(name="duration", type="integer")
     * @Expose
     */
    protected $duration;
    
    /**
     * Genres (format : array("genre1", "genre2", "genre3"))
     * @ORM\Column(name="genres", type="string")
     * @Expose
     */
    protected $genres;
    
    /**
     * Réalisateur
     * @ORM\Column(name="director", type="string")
     * @Expose
     */
    protected $director;
    
    /**
     * Acteurs (format : array("acteur1", "acteur2", "acteur3"))
     * @ORM\Column(name="actors", type="string")
     * @Expose
     */
    protected $actors;
    
    /**
     * Intrigue
     * @ORM\Column(name="description", type="string")
     * @Expose
     */
    protected $description;
    
    /**
     * URL de l'affiche du film/de la série
     * @ORM\Column(name="image", type="string")
     * @Expose
     */
    protected $image;
    
    /**
     * Metascore/classement Imdb (en %)
     * @ORM\Column(name="rating", type="string")
     * @Expose
     */
    protected $rating;
}
