<?php

namespace KI\CoreBundle\Service;

use KI\CoreBundle\Entity\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

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
     * Prend une ressource (url ou Base64) et retourne une image
     * @param  string $src Source depuis laquelle récupérer une image
     * @param  bool   $url Peut forcer l'upload par url (pour outrepasser la regex)
     * @return Image
     * @see KI\CoreBundle\Entity\Image
     */
    public function upload($src, $url = false)
    {
        $fs = new Filesystem();
        $image = new Image();

        // Répartition entre url et Base64
        $regex = '/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/iS';
        if ($url || preg_match($regex, $src)) {
            $data = $this->uploadFromUrl($src);
        } else {
            $data = $this->uploadFromBase64($src);
        }

        $image->setExt($data['extension']);

        // Sauvegarde l'image dans un dossier temporaire, les méthodes (Pre/Post)Persist d'Image font le reste
        $path = $image->getUploadsDirectory().'tmp/'.md5($data['image']);
        $fs->dumpFile($path, $data['image']);
        $file = new File($path);
        $image->setFile($file);

        return $image;
    }

    /**
     * @param string $base64
     * @return array
     */
    public function uploadFromBase64($base64)
    {
        if (empty($base64)) {
            return;
        }

        $imageString = base64_decode($base64);
        $image = imagecreatefromstring($imageString);

        if ($image !== null) {
            $extension = explode('/', getimagesizefromstring($imageString)['mime'])[1];

            return [
                'image' => $imageString,
                'extension' => $extension
            ];
        }
    }

    /**
     * @param string $url
     */
    public function uploadFromUrl($url)
    {
        // Réglage des options cURL
        $data = $this->curlService->curl($url, null, [
            CURLOPT_BUFFERSIZE => 128,
            CURLOPT_NOPROGRESS => true,
            CURLOPT_PROGRESSFUNCTION, function($downloadSize, $downloaded, $uploadSize, $uploaded) {
                // Retourner autre chose que 0 stoppe la connexion
                return ($downloaded > ($this->imagesMaxSize)) ? 1 : 0;
            }
        ]);

        // Récupération de l'image
        if (!$data) {
            throw new \Exception('Impossible de télécharger l\'image à l\'url '.$url);
        }

        // Récupération de l'extension
        $image = imagecreatefromstring($data);

        if ($image !== null) {
            $extension = explode('/', getimagesizefromstring($data)['mime'])[1];
        } else {
            throw new \Exception('Image non reconnue');
        }

        return [
            'image' => $data,
            'extension' => $extension
        ];
    }
}
