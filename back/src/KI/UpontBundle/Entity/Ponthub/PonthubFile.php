<?php

namespace KI\UpontBundle\Entity\Ponthub;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Core\Likeable;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class PonthubFile extends Likeable
{
    protected $fleurDn = 'fleur.enpc.fr';
    protected $fleurPort = 8080;

    /**
     * Chemin complet sur Fleur
     * @ORM\Column(name="path", type="string")
     * @Assert\Type("string")
     */
    protected $path;

    public function fileUrl()
    {
        return 'http://'.$this->fleurDn.':'.$this->fleurPort.str_replace('/root/web', '', $this->path);
    }

    /**
     * Taille en octets
     * @ORM\Column(name="size", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $size;

    /**
     * Date d'ajout (timestamp)
     * @ORM\Column(name="added", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $added;

    /**
     * Statut [OK|NeedInfos|NotFound]
     * @ORM\Column(name="status", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $status;

    /**
     * Description
     * @ORM\Column(name="description", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $description;

    /**
     * Image (affiche/jaquette/screenshot...)
     * @ORM\OneToOne(targetEntity="KI\UpontBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : null;
    }

    /**
     * Tags
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Tag", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listTags;

    /**
     * @JMS\VirtualProperty()
     */
    public function tags()
    {
        $tags = array();
        foreach ($this->listTags as $tag) {
                    $tags[] = $tag->getName();
        }
        return $tags;
    }

    /**
     * Genres
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Ponthub\Genre", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listGenres;

    /**
     * @JMS\VirtualProperty()
     */
    public function genres()
    {
        $genres = array();
        foreach ($this->listGenres as $genre) {
                    $genres[] = $genre->getName();
        }
        return $genres;
    }

    /**
     * Utilisateurs ayant téléchargé le fichier
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $users;

    /**
     * Nombre de fois où le fichier a été téléchargé
     * @JMS\VirtualProperty()
     */
    public function downloads()
    {
        return count($this->users);
    }








    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listGenres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->listTags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Album
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return PonthubFile
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set added
     *
     * @param integer $added
     * @return Album
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return integer
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return PonthubFile
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Album
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param \KI\UpontBundle\Entity\Image $image
     * @return Album
     */
    public function setImage(\KI\UpontBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \KI\UpontBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add tags
     *
     * @param \KI\UpontBundle\Entity\Tag $tag
     * @return Album
     */
    public function addTag(\KI\UpontBundle\Entity\Tag $tag)
    {
        $this->listTags[] = $tag;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \KI\UpontBundle\Entity\Tag $tag
     */
    public function removeTag(\KI\UpontBundle\Entity\Tag $tag)
    {
        $this->listTags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->listTags;
    }

    /**
     * Set tags
     *
     * @return PonthubFile
     */
    public function setTags($tags)
    {
        return $this->listTags = $tags;
    }

    /**
     * Add genres
     *
     * @param \KI\UpontBundle\Entity\Ponthub\Genre $genre
     * @return Album
     */
    public function addGenre(\KI\UpontBundle\Entity\Ponthub\Genre $genre)
    {
        $this->listGenres[] = $genre;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \KI\UpontBundle\Entity\Ponthub\Genre $genre
     */
    public function removeGenre(\KI\UpontBundle\Entity\Ponthub\Genre $genre)
    {
        $this->listGenres->removeElement($genre);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->listGenres;
    }

    /**
     * Set genres
     *
     * @return PonthubFile
     */
    public function setGenres($genres)
    {
        return $this->listGenres = $genres;
    }


    /**
     * Add user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return PonthubFile
     */
    public function addUser(\KI\UpontBundle\Entity\Users\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \KI\UpontBundle\Entity\User $user
     */
    public function removeUser(\KI\UpontBundle\Entity\Users\User $user)
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
     * Set users
     *
     * @return PonthubFile
     */
    public function setUsers($users)
    {
        return $this->users = $users;
    }
}
