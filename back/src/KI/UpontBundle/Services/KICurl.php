<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

class KICurl extends ContainerAware
{
    // Téléchargement d'une ressource externe
    public function curl($url, array $options = array())
    {
        $proxyUrl = $this->container->getParameter('proxy_url');
        $proxyUser = $this->container->getParameter('proxy_user');
        
        // Réglage des options cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_0');
        curl_setopt($ch, CURLOPT_USERAGENT, 'runscope/0.1');
        
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_STDERR, fopen('/var/www/youpont/v2/app/logs/curl.log', 'a'));
        
        foreach($options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        if ($proxyUrl !== null)
            curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
        if ($proxyUser !== null)
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser);
        
        // Récupération de la ressource
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
