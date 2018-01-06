<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class PostFile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @Assert\File(maxSize="2M")
     */
    protected $file;

    /**
     * @var \App\Entity\Post
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="files")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     **/
    private $post;

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return __DIR__.'/../../public/uploads/'.$this->getUploadCategory().'/';
    }

    /**
     * @return string
     */
    protected function getUploadCategory()
    {
        return 'posts';
    }

    public function getAbsolutePath()
    {
        return $this->getUploadDir().$this->post->getId()."_".$this->getName();
    }


    /**
     * @JMS\VirtualProperty()
     * @return string
     */
    public function url()
    {
        return 'uploads/'.$this->getUploadCategory().'/'.$this->post->getId()."_".$this->getName();
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
     * @return PostFile
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
     * @return PostFile
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
     * @return PostFile
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
    }

    /**
     * @ORM\PostPersist()
     */
    public function moveFile() {
        if ($this->file === null) {
            return;
        }

        if (file_exists($this->file->getRealPath())) {
            $this->file->move($this->getUploadDir(), $this->post->getId()."_".$this->getName());
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeFile()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }
}
