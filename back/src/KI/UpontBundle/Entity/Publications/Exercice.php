<?php

namespace KI\UpontBundle\Entity\Publications;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use KI\UpontBundle\Entity\Likeable;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Exercice extends Likeable
{
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
