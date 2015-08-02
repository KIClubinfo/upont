<?php

namespace KI\CoreBundle\Service;

class CurlService
{
    protected $proxyUrl;
    protected $proxyUser;

    public function __construct($proxyUrl, $proxyUser)
    {
        $this->proxyUrl = $proxyUrl;
        $this->proxyUser = $proxyUser;
    }

    // Téléchargement d'une ressource externe
    public function curl($url, $payload = null, array $options = array())
    {
        // Réglage des options cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Nécessaire à cause de la configuration d'Odin
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER,         false);
        curl_setopt($ch, CURLOPT_USERAGENT,      'runscope/0.1');
        curl_setopt($ch, CURLOPT_HTTP_VERSION,   'CURL_HTTP_VERSION_1_0');

        // Ajout d'éventuels champs POST
        if (!empty($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_POST,       true);
        }

        // Réglage éventuel du proxy
        if ($this->proxyUrl !== null) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyUrl);
        }
        if ($this->proxyUser !== null) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser);
        }

        // On ajoute d'éventuelles options si elles sont spécifiées
        foreach ($options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        // Récupération de la ressource
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
