<?php

namespace KI\PonthubBundle\Service;

use KI\PonthubBundle\Service\Gracenote\GracenoteWebAPI;

// Échange des informations avec l'API Gracenote pour récupérer des informations
// sur la musique (utilisé par Ponthub)
// Testé par PonthubControllerTest
class GracenoteService
{
    protected $api;

    public function __construct($gracenoteKey1, $gracenoteKey2, $gracenoteKey3, $proxyUser, $proxyUrl)
    {
        $this->api = new GracenoteWebAPI(
            $gracenoteKey1,
            $gracenoteKey2,
            $gracenoteKey3,
            $proxyUrl,
            $proxyUser
        );
    }

    public function searchAlbum($name, $artistHint = '')
    {
        $response = $this->api->searchAlbum($artistHint, $name, GracenoteWebAPI::BEST_MATCH_ONLY);

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
