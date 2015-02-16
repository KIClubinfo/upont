<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;


// Le service en lui même
class KIImages extends ContainerAware
{
    // Upload d'une image à partir de données en base 64 et renvoie l'extension de l'image
    public function uploadBase64($data)
    {
        // On n'enregistre des données que si elles sont non nulles
      
        if ($data !== null && $data !== '') {
		    $imgstring=base64_decode($data);
            $image = imagecreatefromstring($imgstring);

		    if($image!=null)
		    {
			    $ext=explode("/",getimagesizefromstring($imgstring)['mime'])[1];
		    }
        }
        $result=array();
        $result['image']=$imgstring;
        $result['extension']=$ext;

        return  $result;
    }
    
    // Upload d'une image à partir d'une URL et renvoie l'image sous forme de string et son extension
    public function uploadUrl($url)
    {

		//Récupération des paramètres proxy
        $proxyUrl = $this->container->getParameter('proxy_url');
        $proxyUser = $this->container->getParameter('proxy_user');
        
        // Réglage des options cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);

        if ($proxyUrl !== null)
            curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
        if ($proxyUser !== null)
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser);
        
        // Limitation de la taille 
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128); // more progress info
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($DownloadSize, $Downloaded, $UploadSize, $Uploaded ){
            // If $Downloaded exceeds $this->container->getParameter('upont_images_maxSize') B, returning non-0 breaks the connection!
            return ($Downloaded > ($this->container->getParameter('upont_images_maxSize'))) ? 1 : 0;
        });

        // Récupération de l'image
        if( ! $dataCurl = curl_exec($ch))
        {
              throw new \Exception('Erreur de curl'.curl_error($ch));
        }
        curl_close($ch);

	    
		//Récupération de l'extension
	  
		$image = imagecreatefromstring($dataCurl);
		if($image!=null)
		{
			$ext=explode("/",getimagesizefromstring($dataCurl)['mime'])[1];
		}
		else
			throw new \Exception('Image non reconnue');

		$result=array();
        $result['image']=$dataCurl;
        $result['extension']=$ext;

	    return $result;
    }
    
    // Suppression d'une image
     public function remove($path)
    {
        if (file_exists($path))
            return unlink($path);
        return false;
    }
}
