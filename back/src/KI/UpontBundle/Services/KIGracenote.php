<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use KI\UpontBundle\Services\Gracenote\GracenoteWebAPI;
use KI\UpontBundle\Services\Gracenote\GNException;
use KI\UpontBundle\Services\Gracenote\GNError;
use KI\UpontBundle\Services\Gracenote\HTTP;

// Échange des informations avec l'API Gracenote pour récupérer des informations
// sur la musique (utilisé par Ponthub)
// Testé par PonthubControllerTest
class KIGracenote extends ContainerAware
{
    public function searchAlbum($name, $artistHint = '')
    {
        $api = new GracenoteWebAPI(
            $this->container->getParameter('upont_gracenote_key1'),
            $this->container->getParameter('upont_gracenote_key2'),
            $this->container->getParameter('upont_gracenote_key3'),
            $this->container->getParameter('proxy_url'),
            $this->container->getParameter('proxy_user')
        );

        $response = $api->searchAlbum($artistHint, $name, GracenoteWebAPI::BEST_MATCH_ONLY);

        // On garde le premier résultat, c'est le plus pertinent
        if (count($response) > 0) {
            return array(
                'name' => $response[0]['album_title'],
                'artist' => $response[0]['album_artist_name'],
                'year' => $response[0]['album_year'],
                'image' => preg_replace('#\?.*$#Ui', '', $response[0]['album_art_url']),
            );
        }

        return null;
    }
}
