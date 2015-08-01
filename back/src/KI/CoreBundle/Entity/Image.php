<?php

namespace KI\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="ext", type="string")
     * @Assert\Type("string")
     */
    protected $ext;

    /**
     * @ORM\Column(name="alt", type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $alt;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    public function getAbsolutePathReduced()
    {
        return __DIR__.'/../../../../web/uploads/images/'.$this->id;
    }

    public function getAbsolutePath()
    {
        return __DIR__.'/../../../../web/uploads/images/'.$this->id.'.'.$this->ext;
    }

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/uploads/images/';
    }


    public function getTemporaryDir()
    {
        return __DIR__.'/../../../../web/uploads/tmp/';
    }

    public function getWebPath()
    {
        return 'uploads/images/'.$this->id.'.'.$this->ext;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if ($this->file === null)
            throw new \Exception('Il n\'y a aucun fichier');
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
            $this->file->move($this->getUploadRootDir(), $this->id.'.'.$this->ext);
            unset($this->file);
            $this->createThumbnail($this->getAbsolutePath());
        }
    }




    // Temporary name that is used to remove the file
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
        if ($file = $this->filenameForRemove)
            unlink($file);
    }

    // Setter/Getter non généré automatiquement
    public function getFile()
    {
        return $this->file;
    }

    public function setFile($newFile)
    {
        $this->file = $newFile;
        return $this;
    }

    // Crée une miniature pour l'image de chemin $path
    // Dans un dossier thumbnails
    static public function createThumbnail($path)
    {
        $extension = pathinfo(strtolower($path), PATHINFO_EXTENSION);

        if (preg_match('/jpg|jpeg/',$extension))
            $image = imagecreatefromjpeg($path);
        else if (preg_match('/png/',$extension))
            $image = imagecreatefrompng($path);
        else
            throw new BadRequestHttpException('Extension non reconnue !');

        // Redimensionnement de l'image
        $maxWidth = 200;
        $mawHeight = 200;
        list($imageWidth, $imageHeight) = getimagesize($path);

        $thumbWidth = $imageWidth;
        $thumbHeight = $imageHeight;

        if ($thumbHeight > $mawHeight) {
            $thumbWidth = floor($thumbWidth * $mawHeight / $thumbHeight);
            $thumbHeight = $mawHeight;
        }

        if ($thumbWidth > $maxWidth) {
            $thumbHeight = floor($thumbHeight * $maxWidth / $thumbWidth);
            $thumbWidth = $maxWidth;
        }

        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imageWidth, $imageHeight);

        // Enregistrement de la miniature
        $thumbPath = dirname(str_replace('images', 'thumbnails', $path)).'/';
        // Création du dossier thumbnails au besoin
        if(!is_dir($thumbPath)) mkdir($thumbPath);

        if (preg_match('/jpg|jpeg/', $extension))
            imagejpeg($thumbnail, $thumbPath . substr($path, strlen(dirname($path)) + 1));
        else
            imagepng($thumbnail, $thumbPath . substr($path, strlen(dirname($path)) + 1));

        imagedestroy($image);
        imagedestroy($thumbnail);
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
     * Set ext
     *
     * @param string $ext
     * @return Image
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
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }
}
