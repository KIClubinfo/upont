<?php

namespace KI\PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class NewsitemFile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @ORM\Column(name="ext", type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $ext;

    /**
     * @ORM\Column(name="name", type="string")
     * @Assert\Type("string")
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     * @JMS\Expose
     */
    private $size;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var \KI\PublicationBundle\Entity\Newsitem
     * @ORM\ManyToOne(targetEntity="KI\PublicationBundle\Entity\Newsitem", inversedBy="files")
     * @ORM\JoinColumn(name="newsitem_id", referencedColumnName="id")
     **/
    private $newsitem;

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return __DIR__.'/../../../../web/uploads/'.$this->getUploadCategory().'/';
    }

    /**
     * @return string
     */
    protected function getUploadCategory()
    {
        return 'newsitems';
    }

    public function getAbsolutePath()
    {
        return $this->getUploadDir().$this->getId();
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
     * Set ext
     *
     * @param string $ext
     * @return NewsitemFile
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return NewsitemFile
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
     * Set size
     *
     * @param integer $size
     * @return NewsitemFile
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
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
        $this->setSize($this->file->getClientSize());
        $this->setExt($this->file->getExtension());
        $this->setName($this->file->getClientOriginalName());
    }

    /**
     * @ORM\PostPersist()
     */
    public function moveFile(){
        if (file_exists($this->file->getRealPath())) {
            $this->file->move($this->getUploadDir(), $this->id);
            unset($this->file);
        }
    }

    /**
     * Variable utilisée pour stocker le nom du fichier de manière provisioire
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
     * @return mixed
     */
    public function getNewsitem()
    {
        return $this->newsitem;
    }

    /**
     * @param mixed $newsitem
     */
    public function setNewsitem($newsitem)
    {
        $this->newsitem = $newsitem;
    }
}
