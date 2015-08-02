<?php

namespace KI\CoreBundle\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use KI\CoreBundle\Entity\Image;

class ImageService
{
    protected $curlService;
    protected $imagesMaxSize;

    public function __construct(CurlService $curlService, $imagesMaxSize)
    {
        $this->curlService = $curlService;
        $this->imagesMaxSize = $imagesMaxSize;
    }

    /**
     * @param string $src
     */
    public function upload($src, $url = null)
    {
        $fs = new Filesystem();
        $image = new Image();
        // Checks if the input is an URL or not
        // Returns an array with the image and the extension
        $regex = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS';
        if ($url || preg_match($regex, $src))
            $data = $this->uploadUrl($src, true);
        else
            $data = $this->uploadBase64($src);

        $image->setExt($data['extension']);

        // Saves the image locally thanks to md5 hash and puts it in the $image
        $path = $image->getTemporaryDir().md5($data['image']);
        $fs->dumpFile($path, $data['image']);
        $file = new File($path);
        $image->setFile($file);

        return $image;
    }

    // Upload d'une image à partir de données en base 64
    // Renvoie l'extension de l'image
    public function uploadBase64($data)
    {
        // On n'enregistre des données que si elles sont non nulles
        if ($data !== null && $data !== '') {
            $imgString = base64_decode($data);
            $image = imagecreatefromstring($imgString);

            if ($image !== null) {
                $ext = explode('/', getimagesizefromstring($imgString)['mime'])[1];

                return array(
                    'image' => $imgString,
                    'extension' => $ext
                );
            }
        }
        return null;
    }

    // Upload d'une image à partir d'une URL
    // Renvoie l'image sous forme de string et son extension
    public function uploadUrl($url, $byPassCheck = false)
    {
        if (!($byPassCheck || preg_match('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS', $url)))
            throw new BadRequestHttpException('Ceci n\'est pas une url : '.$url);

        // Réglage des options cURL
        $data = $this->curlService->curl($url, null, array(
            CURLOPT_BUFFERSIZE => 128,
            CURLOPT_NOPROGRESS => true,
            CURLOPT_PROGRESSFUNCTION, function($downloadSize, $downloaded, $uploadSize, $uploaded) {
                // If downloaded exceeds image max size, returning non-0 breaks the connection!
                return ($downloaded > ($this->imagesMaxSize)) ? 1 : 0;
            }
        ));

        // Récupération de l'image
        if (!$data)
            throw new \Exception('Impossible de télécharger l\'image à l\'url '.$url);

        //Récupération de l'extension
        $image = imagecreatefromstring($data);
        if ($image !== null)
            $ext = explode('/', getimagesizefromstring($data)['mime'])[1];
        else
            throw new \Exception('Image non reconnue');

        return array(
            'image' => $data,
            'extension' => $ext
        );
    }

    // Suppression d'une image
    public function remove($path)
    {
        if (file_exists($path))
            return unlink($path);
        return false;
    }

    // Crée des miniatures pour toutes les images du dossier $path
    // dans le dossier {path}/thumbnails
    public function createThumbnails($path)
    {
        $images = array();
        $images = scandir($path);

        foreach ($images as $image) {
            $extension = pathinfo(strtolower($image), PATHINFO_EXTENSION);

            if (is_file(str_replace('images', 'thumbnails', $path).'/'.$image)
                || ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png')
                ) continue;

            Image::createThumbnail($path.'/'.$image);
        }
    }
}
