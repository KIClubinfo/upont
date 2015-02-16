<?php

namespace KI\UpontBundle\Entity\Ponthub;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ExclusionPolicy("all")
 */
class PonthubFile
{
    protected $fleurDn = 'fleur.enpc.fr';
    protected $fleurPort = 8080;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Chemin complet sur Fleur
     * @ORM\Column(name="path", type="string")
     * @Assert\Type("string")
     */
    protected $path; 

    public function fileUrl()
    {
        return 'http://' . $this->fleurDn . ':' . $this->fleurPort . str_replace('/root/web', '', $this->path);
    }
    
    /**
     * Nom apparent
     * @ORM\Column(name="name", type="string")
     * @Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * Slug
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;
    
    /**
     * Taille en octets
     * @ORM\Column(name="size", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $size;
    
    /**
     * Date d'ajout (timestamp)
     * @ORM\Column(name="added", type="integer", nullable=true)
     * @Expose
     * @Assert\Type("integer")
     */
    protected $added;
    
    /**
     * Statut [OK|NeedInfos|NotFound]
     * @ORM\Column(name="status", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $status;

    /**
     * Description
     * @ORM\Column(name="description", type="string", nullable=true)
     * @Expose
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
     * @VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : 'uploads/images/default-user.png';
    }
    
    /**
     * Tags
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Tag", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listTags;
    
    /**
     * @VirtualProperty()
     */
    public function tags()
    {
        $tags = array();
        foreach($this->listTags as $tag)
            $tags[] = $tag->getName();
        return $tags;
    }
    
    /**
     * Genres
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Ponthub\Genre", cascade={"persist"})
     * @Assert\Valid()
     */
    protected $listGenres;
    
    /**
     * @VirtualProperty()
     */
    public function genres()
    {
        $genres = array();
        foreach($this->listGenres as $genre)
            $genres[] = $genre->getName();
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
     * @VirtualProperty()
     */
    public function downloads()
    {
        return count($this->users);
    }
    
    /**
     * Ceux qui likent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="ponthub_likes",
     *      joinColumns={@ORM\JoinColumn(name="ponthub_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="liker_id", referencedColumnName="id")}
     * )
     */
    protected $listLikes;
    
    /**
     * Nombre de ceux qui likent
     * @VirtualProperty()
     */
    public function likes()
    {
        return count($this->listLikes);
    }
    
    /**
     * Ceux qui unlikent
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\User", cascade={"persist"})
     * @ORM\JoinTable(name="ponthub_unlikes",
     *      joinColumns={@ORM\JoinColumn(name="ponthub_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="unliker_id", referencedColumnName="id")}
     *  )
     */
    protected $listUnlikes;

    /**
     * Nombre de ceux qui unlikent
     * @VirtualProperty()
     */
    public function unlikes()
    {
        return count($this->listUnlikes);
    }
    
    /**
     * @Expose
     */
    protected $like = false;
    
    /**
     * @Expose
     */
    protected $unlike = false;
    
    
    
    
    
    
    
    
    //===== GENERATED AUTOMATICALLY =====//
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listGenres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->listTags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->unlikes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Album
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
     * Set name
     *
     * @param string $name
     * @return Album
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Album
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
     * @param boolean $status
     * @return Album
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
     * @param \KI\UpontBundle\Entity\Tag $tags
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
     * @param \KI\UpontBundle\Entity\Tag $tags
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
     * @param \KI\UpontBundle\Entity\Ponthub\Genre $genres
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
     * @param \KI\UpontBundle\Entity\Ponthub\Genre $genres
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
     * @param \KI\UpontBundle\Entity\User $users
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
     * @param \KI\UpontBundle\Entity\User $users
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
    
    /**
     * Add like
     *
     * @param \KI\UpontBundle\Entity\User $likes
     * @return PonthubFile
     */
    public function addLike(\KI\UpontBundle\Entity\Users\User $like)
    {
        $this->listLikes[] = $like;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \KI\UpontBundle\Entity\User $likes
     */
    public function removeLike(\KI\UpontBundle\Entity\Users\User $like)
    {
        $this->listLikes->removeElement($like);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->listLikes;
    }
    
    /**
     * Set likes
     *
     * @return PonthubFile
     */
    public function setLikes($likes)
    {
        return $this->listLikes = $likes;
    }
    
    /**
     * Add unlike
     *
     * @param \KI\UpontBundle\Entity\User $unlikes
     * @return PonthubFile
     */
    public function addUnlike(\KI\UpontBundle\Entity\Users\User $unlike)
    {
        $this->listUnlikes[] = $unlike;

        return $this;
    }

    /**
     * Remove unlikes
     *
     * @param \KI\UpontBundle\Entity\User $unlikes
     */
    public function removeUnlike(\KI\UpontBundle\Entity\Users\User $unlike)
    {
        $this->listUnlikes->removeElement($unlike);
    }

    /**
     * Get unlikes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnlikes()
    {
        return $this->listUnlikes;
    }
    
    /**
     * Set unlikes
     *
     * @return PonthubFile
     */
    public function setUnlikes($unlikes)
    {
        return $this->listUnlikes = $unlikes;
    }
    
    public function getUnlike()
    {
        return $this->unlike;
    }
    
    public function setUnlike($unlike)
    {
        return $this->unlike = $unlike;
    }
    
    public function getLike()
    {
        return $this->like;
    }
    
    public function setLike($like)
    {
        return $this->like = $like;
    }
}
