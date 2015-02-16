<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

// Échange des informations avec l'API Imdb pour récupérer des informations
// sur les films et les séries (utilisé par Ponthub)
// Testé par PonthubControllerTest
class KIImdb extends ContainerAware
{
    protected $baseUrl = 'http://www.omdbapi.com/';
    
    public function search($name)
    {
        $url = $this->baseUrl . '?s=' . urlencode($name);
        $curl = $this->container->get('ki_upont.curl');
        $response = json_decode($curl->curl($url), true);
        
        $return  = array();
        foreach($response['Search'] as $result) {
            $return[] = array(
                'name' => $result['Title'],
                'year' => $result['Year'],
                'type' => $result['Type'],
                'id'   => $result['imdbID']
            );
        }

        return $return;
    }
    
    public function infos($id)
    {
        $url = $this->baseUrl . '?i=' . urlencode($id);
        $curl = $this->container->get('ki_upont.curl');
        $response = json_decode($curl->curl($url), true);
        
        if (!isset($response['Title']))
            return null;

        return array(
            'title'       => $response['Title'],
            'year'        => $response['Year'],
            'duration'    => 60*str_replace(' min', '', $response['Runtime']),
            'genres'      => explode(', ', $response['Genre']),
            'director'    => $response['Director'],
            'actors'      => explode(', ', $response['Actors']),
            'description' => $response['Plot'],
            'image'       => $response['Poster'],
            'rating'      => isset($response['Metascore']) ? $response['Metascore'] : $response['imdbRating']*10
        );
    }
}
