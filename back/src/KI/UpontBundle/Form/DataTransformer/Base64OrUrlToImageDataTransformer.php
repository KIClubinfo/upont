<?php
namespace KI\UpontBundle\Form\DataTransformer;

use KI\UpontBundle\Services\KIImages;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Image;


class Base64OrUrlToImageDataTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    

    private $uploaderService;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om,KIImages $uploaderService)
    {
        $this->om = $om;
        $this->uploaderService = $uploaderService;
    }

    /**
     * En théorie, ne doit pas être utilisé.
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($img)
    {
        if (null === $img)
            return '';

        return $img->getWebPath();
    }

    /**
     * Transforms an url/base64 to an Image.
     *
     * @param  string $input
     * @return Image|null
     * @throws TransformationFailedException if Image is not good/uploaded.
     */
    public function reverseTransform($Base64orUrl)
    {
        if (!$Base64orUrl)
            return null;
    
        
        $fs = new Filesystem();
        $img = new Image();
        
        // Check if the input is an URL or not and return an array with the image and the extension
        if (preg_match('#^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$#', $Base64orUrl)) 
            $imgArray = $this->uploaderService->uploadUrl($Base64orUrl);
        else
            $imgArray = $this->uploaderService->uploadBase64($Base64orUrl);

        $img->setExt($imgArray['extension']);

        // Save the image locally thanks to md5 hash and put it in the $img
        $temporaryPath=$img->getTemporaryDir() . md5($imgArray['image']);
        $fs->dumpFile($temporaryPath, $imgArray['image']);
        $imgFile = new File($temporaryPath);
        $img->setFile($imgFile);
        return $img;
    }        
}
