<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use KI\UserBundle\Entity\Club;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class Newsitem extends Post
{
    /**
     * Au nom de quel club a été publié l'event, null si aucun club
     * @var \KI\UserBundle\Entity\Club
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\Club", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorClub;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="KI\PublicationBundle\Entity\NewsitemFile", mappedBy="newsitem", cascade={"persist", "remove"})
     * @JMS\Expose
     */
    private $files;

    /**
     * @var ArrayCollection
     */
    private $uploadedFiles;

    /**
     * Délivre l'url de :
     * - l'image du club
     * - l'image d'utilisateur par défaut sinon
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->authorClub !== null && $this->authorClub->getImage() !== null) {
            return $this->authorClub->getImage()->getWebPath();
        }

        return 'uploads/others/default-user.png';
    }

    public function __construct()
    {
        parent::__construct();

        $this->files = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
    }

    /**
     * Set authorClub
     *
     * @param \KI\UserBundle\Entity\Club $authorClub
     * @return Newsitem
     */
    public function setAuthorClub(\KI\UserBundle\Entity\Club $authorClub = null)
    {
        $this->authorClub = $authorClub;

        return $this;
    }

    /**
     * Get authorClub
     *
     * @return \KI\UserBundle\Entity\Club
     */
    public function getAuthorClub()
    {
        return $this->authorClub;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    /**
     * @return ArrayCollection
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @param ArrayCollection $uploadedFiles
     */
    public function setUploadedFiles($uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @ORM\PrePersist()
     */
    public function upload()
    {
        if (is_array($this->uploadedFiles))
        {
            foreach ($this->uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $file = new NewsitemFile();
                    $file->setFile($uploadedFile);
                    $file->setNewsitem($this);

                    $this->getFiles()->add($file);
                }
            }
        }
    }
}
