<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks
 */
class Post extends Likeable
{
    /**
     * Au nom de quel club a été publié l'event, null si aucun club
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\Club", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     */
    protected $authorClub;

    /**
     * Auteur réel
     * @ORM\ManyToOne(targetEntity="KI\UserBundle\Entity\User", cascade={"persist"})
     * @JMS\Expose
     * @Assert\Valid()
     *
     * @var \KI\UserBundle\Entity\Club
     */
    protected $authorUser;
    protected $autoSetUser = 'authorUser';
    public function getAutoSetUser() { return $this->autoSetUser; }

    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    /**
     * Corps du texte
     * @ORM\Column(name="text", type="text")
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * @var PostFile
     *
     * @ORM\OneToMany(targetEntity="KI\PublicationBundle\Entity\PostFile", mappedBy="post", cascade={"persist", "remove"})
     *
     */
    private $files;

    /**
     * @var ArrayCollection
     */
    private $uploadedFiles;

    /**
     * Délivre l'url de l'image du post par défaut :
     * - L'url de l'image du club
     * - null sinon
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

        $this->date = time();
        $this->files = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Newsitem
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
     * Set text
     *
     * @param string $text
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
     * Set authorUser
     *
     * @param \KI\UserBundle\Entity\User $authorUser
     * @return Newsitem
     */
    public function setAuthorUser(\KI\UserBundle\Entity\User $authorUser = null)
    {
        $this->authorUser = $authorUser;

        return $this;
    }

    /**
     * Get authorUser
     *
     * @return \KI\UserBundle\Entity\User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
    }

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
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (is_array($this->uploadedFiles))
        {
            foreach ($this->uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $file = new PostFile($uploadedFile, $this->getId());
                    $this->getFiles()->add($file);
                    $file->setPost($this);
                    unset($uploadedFile);
                }
            }
        }
    }
}
