<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Exercice extends Likeable
{
    /**
     * Utilisateur qui a uploadé l'annale
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $uploader;

    /**
     * Date de l'upload
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Le cours parent
     * @ORM\ManyToOne(targetEntity="KI\PublicationBundle\Entity\Course", cascade={"persist"}, inversedBy="exercices")
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
        return __DIR__.'/../../../../../web/uploads/exercices/';
    }

    public function getAbsolutePath()
    {
        return __DIR__.'/../../../../../web/uploads/exercices/'.$this->id.'.pdf';
    }

    public function getWebPath()
    {
        return 'uploads/exercices/'.$this->id.'.pdf';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if ($this->file === null)
            return;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if ($this->file === null)
            return;

        // Exception lancée si le fichier ne peut pas être bougé et donc
        // arrête le Persist
        if (file_exists($this->file->getRealPath())) {
            $this->file->move($this->getBasePath(), $this->id.'.pdf');
            unset($this->file);
        }
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
     * @param \KI\UserBundle\Entity\User $uploader
     * @return Exercice
     */
    public function setUploader(\KI\UserBundle\Entity\User $uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * Get uploader
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * Set course
     *
     * @param \KI\PublicationBundle\Entity\Course $course
     * @return Exercice
     */
    public function setCourse(\KI\PublicationBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \KI\PublicationBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }
}
