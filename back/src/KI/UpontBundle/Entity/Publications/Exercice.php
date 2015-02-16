<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Exercice
{   
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Utilisateur qui a uploadé l'annale
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User")
     * @Expose
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploader;
    
    /**
     * Date de l'upload
     * @ORM\Column(name="date", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;
    
    /**
     * Nom de l'annale
     * @ORM\Column(name="name", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $name;
    
    /**
     * Département dans lequel l'annale a été posée
     * @ORM\Column(name="department", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $department;

    /**
     * Indique si l'annale a été validée ou non
     * @ORM\Column(name="valid", type="boolean", nullable=true)
     * @Expose
     * @Assert\Type("boolean")
     */
    protected $valid;
    
    /**
     * @Gedmo\Slug(fields={"department","name"})
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $slug;
    
    public function getBasePath()
    {
        return __DIR__.'/../../../../../web/uploads/exercices/';
    }
    
    public function getAbsolutePath()
    {
        return __DIR__.'/../../../../../web/uploads/exercices/' . $this->id . '.pdf';
    }

    public function getWebPath()
    {
        return 'uploads/exercices/' . $this->id . '.pdf';
    }
    
    // Variable temporaire pour la suppression du fichier
    protected $filenameForRemove;
    
    /** 
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * Méthode en postRemove pour être sûr que ça soit être soit supprimé après le flush
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->filenameForRemove)
            unlink($this->filenameForRemove);
    }
    
    
    
    
    /**
     * Propriété dummy pour valider le formulaire, l'upload réel se fait par le controleur
     * @Assert\File(maxSize="6000000")
     */
    protected $file;
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile($newFile)
    {
        $this->file = $newFile;
        return $this;
    }








    //===== GENERATED AUTOMATICALLY =====//

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
     * Set date
     *
     * @param integer $date
     * @return Exercice
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Exercice
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
     * Set department
     *
     * @param string $department
     * @return Exercice
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set valid
     *
     * @param boolean $valid
     * @return Exercice
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return boolean 
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Exercice
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
     * Set uploader
     *
     * @param \KI\UpontBundle\Entity\User $uploader
     * @return Exercice
     */
    public function setUploader(\KI\UpontBundle\Entity\Users\User $uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * Get uploader
     *
     * @return \KI\UpontBundle\Entity\User 
     */
    public function getUploader()
    {
        return $this->uploader;
    }
}
