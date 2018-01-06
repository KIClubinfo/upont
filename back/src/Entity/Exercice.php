<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Exercice extends Likeable
{
    /**
     * Utilisateur qui a uploadé l'annale
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $uploader;
    protected $autoSetUser = 'uploader';
    public function getAutoSetUser() { return $this->autoSetUser; }

    /**
     * Date de l'upload
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Le cours parent
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", cascade={"persist"}, inversedBy="exercices")
     * Comme on veut éviter que l'entité se join sur sa propre colonne
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $course;

    /**
     * Indique si l'annale a été validée ou non
     * @ORM\Column(name="valid", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $valid;

    public function getBasePath()
    {
        return __DIR__.'/../../public/uploads/exercices/';
    }

    public function getAbsolutePath()
    {
        return $this->getBasePath().$this->id.'.pdf';
    }

    public function getWebPath()
    {
        return 'uploads/exercices/'.$this->id.'.pdf';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function checkFileuploadIsPresent()
    {
        if ($this->file === null) {
            return;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function saveNewExercice()
    {
        if ($this->file === null) {
            return;
        }

        // Exception lancée si le fichier ne peut pas être bougé et donc
        // arrête le Persist
        if (file_exists($this->file->getRealPath())) {
            $this->file->move($this->getBasePath(), $this->id.'.pdf');
            unset($this->file);
        }
    }

    /**
     * Variable utilisée pour stocker le fichier image de manière provisioire
     */
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
        if ($this->filenameForRemove) {
            unlink($this->filenameForRemove);
        }
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

    public function __construct()
    {
        parent::__construct();
        $this->date = time();
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
     * Set uploader
     *
     * @param \App\Entity\User $uploader
     * @return Exercice
     */
    public function setUploader(\App\Entity\User $uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * Get uploader
     *
     * @return \App\Entity\User
     */
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * Set course
     *
     * @param \App\Entity\Course $course
     * @return Exercice
     */
    public function setCourse(\App\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \App\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }
}
