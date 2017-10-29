<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use KI\CoreBundle\Entity\Likeable;
use Symfony\Component\Validator\Constraints as Assert;

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
     * Etat de la publication [draft|scheduled|published|emailed]
     * @ORM\Column(name="publicationState", type="string", nullable=true, options={"default" = "published"})
     * @Assert\Type("string")
     * @JMS\Expose
     */
    protected $publicationState;
    const STATE_ORDER = ['draft' => 1, 'scheduled' => 2, 'published' => 3, 'emailed' => 4];

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="KI\PublicationBundle\Entity\PostFile", mappedBy="post", cascade={"persist", "remove"})
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

    public function setFiles($files)
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
     * @ORM\PreFlush()
     * FIXME :D
     */
    public function upload()
    {
        if (is_array($this->uploadedFiles))
        {
            foreach ($this->uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $file = new PostFile($uploadedFile);
                    $file->setFile($uploadedFile);
                    $file->setSize($uploadedFile->getClientSize());
                    $file->setExt($uploadedFile->guessExtension());
                    $file->setName($uploadedFile->getClientOriginalName());
                    $file->setPost($this);

                    $this->getFiles()->add($file);
                }
            }

            $this->uploadedFiles = [];
        }
    }

    /**
     * Set publicationState
     *
     * @param string $publicationState
     *
     * @return Event
     */
    public function setPublicationState($publicationState)
    {
        if (array_key_exists($publicationState, $this::STATE_ORDER)) {
            if (isset($this->publicationState) && $this::STATE_ORDER[$publicationState] < $this::STATE_ORDER[$this->publicationState]) {
                return;
            }
            return $this->publicationState = $publicationState;
        }
        else {
            return;
        }
    }

    /**
     * Get publicationState
     *
     * @return string
     */
    public function getPublicationState()
    {
        return $this->publicationState;
    }

    /**
     * Get sendMail
     *
     * @return boolean
     */
    public function getSendMail()
    {
        return ($this->getPublicationState() == 'emailed');
    }
}
