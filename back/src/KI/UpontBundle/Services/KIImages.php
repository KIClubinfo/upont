<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use KI\UpontBundle\Entity\Image;

class KIImages extends ContainerAware
{
    public function upload($src, $url = null)
    {
        $fs = new Filesystem();
        $image = new Image();

        // Check if the input is an URL or not and return an array with the image and the extension
        $regex = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS';
        if ($url || preg_match($regex, $src))
            $data = $this->uploadUrl($src, true);
        else
            $data = $this->uploadBase64($src);

        $image->setExt($data['extension']);

        // Save the image locally thanks to md5 hash and put it in the $img
        $path = $image->getTemporaryDir().md5($data['image']);
        $fs->dumpFile($path, $data['image']);
        $file = new File($path);
        $image->setFile($file);
        return $image;
    }

    // Upload d'une image à partir de données en base 64 et renvoie l'extension de l'image
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

    // Upload d'une image à partir d'une URL et renvoie l'image sous forme de string et son extension
    public function uploadUrl($url, $byPassCheck = false)
    {
        if (!($byPassCheck || preg_match('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS', $url)))
            throw new BadRequestHttpException('Ceci n\'est pas une url : '.$url);

        $curl = $this->container->get('ki_upont.curl');

        // Réglage des options cURL
        $data = $curl->curl($url, array(
            CURLOPT_BUFFERSIZE => 128,
            CURLOPT_NOPROGRESS => true,
            CURLOPT_PROGRESSFUNCTION, function($downloadSize, $downloaded, $uploadSize, $uploaded) {
                // If $downloaded exceeds $this->container->getParameter('upont_images_maxSize') B, returning non-0 breaks the connection!
                return ($downloaded > ($this->container->getParameter('upont_images_maxSize'))) ? 1 : 0;
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
}
