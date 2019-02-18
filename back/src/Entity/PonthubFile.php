<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PonthubFileRepository")
 * @JMS\ExclusionPolicy("all")
 */
class PonthubFile extends Likeable
{
    protected $fleurDn = 'fleur.enpc.fr';
    protected $fleurPort = 8080;

    /**
     * Chemin complet sur Fleur
     * @ORM\Column(name="path", type="string")
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $path;

    public function fileUrl()
    {
        return 'http://'.$this->fleurDn.':'.$this->fleurPort.str_replace('/root/web', '', $this->path);
    }

    /**
     * Taille en octets
     * @ORM\Column(name="size", type="bigint", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
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
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listTags;

    /**
     * @JMS\VirtualProperty()
     */
    public function tags()
    {
        $tags = [];
        if(is_array($this->listTags) || is_object($this->listTags)) {
            foreach ($this->listTags as $tag) {
                $tags[] = $tag->getName();
            }
        }
        return $tags;
    }

    /**
     * Genres
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listGenres;

    /**
     * @JMS\VirtualProperty()
     */
    public function genres()
    {
        $genres = [];
        foreach ($this->listGenres as $genre) {
                    $genres[] = $genre->getName();
        }
        return $genres;
    }

    /**
     * Utilisateurs ayant téléchargé le fichier
     * @ORM\OneToMany(targetEntity="App\Entity\PonthubFileUser", mappedBy="file", cascade={"remove"})
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

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->listGenres = new ArrayCollection();
        $this->listTags = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return PonthubFile
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
     * @return PonthubFile
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
     * @return PonthubFile
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
     * @param Image $image
     * @return PonthubFile
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add tags
     *
     * @param Tag $tag
     * @return PonthubFile
     */
    public function addTag(Tag $tag)
    {
        $this->listTags[] = $tag;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->listTags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return ArrayCollection
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
     * @param Genre $genre
     * @return PonthubFile
     */
    public function addGenre(Genre $genre)
    {
        $this->listGenres[] = $genre;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param Genre $genre
     */
    public function removeGenre(Genre $genre)
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
     * @param User $user
     * @return PonthubFile
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove users
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return ArrayCollection
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

     /**
     * @JMS\Expose
     */
    protected $downloaded = false;

    public function hasBeenDownloaded()
    {
        return $this->downloaded;
    }

    public function setDownloaded($downloaded)
    {
        return $this->downloaded = $downloaded;
    }
}
