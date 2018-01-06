<?php

namespace App\Entity;

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

    protected $uploadsDirectory;
    public function __construct() {
        $this->uploadsDirectory = __DIR__.'/../../public/uploads/';
    }

    public function getUploadsDirectory()
    {
        return $this->uploadsDirectory;
    }

    public function getAbsolutePath()
    {
        // We need to set complete path because this function is called without the constructor by PreRemove
        return __DIR__.'/../../public/uploads/images/'.$this->id.'.'.$this->ext;
    }

    public function getWebPath()
    {
        return 'uploads/images/'.$this->id.'.'.$this->ext;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @see App\Service\ImageService
     */
    public function checkFileuploadIsPresent()
    {
        if ($this->file === null) {
            throw new \Exception('Il n\'y a aucun fichier');
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     * @see App\Service\ImageService
     */
    public function saveNewImage()
    {
        if ($this->file === null) {
            return;
        }

        if (file_exists($this->file->getRealPath())) {
            $this->file->move($this->uploadsDirectory.'images/', $this->id.'.'.$this->ext);
            unset($this->file);
            $this->createThumbnail($this->getAbsolutePath());
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
        if ($file = $this->filenameForRemove) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
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

    /**
     *  Crée une miniature pour l'image dans un dossier thumbnails
     *  @param  string $originalPath    Le chemin de l'image non réduite
     *  @throws BadRequestHttpException Si l'extension n'est pas prise en charge
     */
    static public function createThumbnail($originalPath)
    {
        $extension = pathinfo(strtolower($originalPath), PATHINFO_EXTENSION);

        if (preg_match('/jpg|jpeg/', $extension)) {
            $image = imagecreatefromjpeg($originalPath);
        } else if (preg_match('/png/', $extension)) {
            $image = imagecreatefrompng($originalPath);
        } else {
            throw new BadRequestHttpException('Extension non reconnue !');
        }

        // Redimensionnement de l'image
        $maxWidth = 200;
        $mawHeight = 200;
        list($imageWidth, $imageHeight) = getimagesize($originalPath);

        $thumbWidth = $imageWidth;
        $thumbHeight = $imageHeight;

        if ($thumbHeight > $mawHeight) {
            $thumbWidth = floor($thumbWidth*$mawHeight/$thumbHeight);
            $thumbHeight = $mawHeight;
        }

        if ($thumbWidth > $maxWidth) {
            $thumbHeight = floor($thumbHeight*$maxWidth/$thumbWidth);
            $thumbWidth = $maxWidth;
        }

        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Activation de la transparence
        if (preg_match('/png/', $extension)) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imageWidth, $imageHeight);

        // Enregistrement de la miniature
        $thumbnailsDirectory = dirname(str_replace('images', 'thumbnails', $originalPath)).'/';
        $thumbnailPath = $thumbnailsDirectory.substr($originalPath, strlen(dirname($originalPath)) + 1);

        // Création du dossier thumbnails au besoin
        if (!is_dir($thumbnailsDirectory)) {
            mkdir($thumbnailsDirectory);
        }

        if (preg_match('/jpg|jpeg/', $extension)) {
            imagejpeg($thumbnail, $thumbnailPath);
        } else {
            imagepng($thumbnail, $thumbnailPath);
        }

        imagedestroy($image);
        imagedestroy($thumbnail);
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
