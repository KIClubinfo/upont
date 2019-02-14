<?php

namespace App\Service;

// Échange des informations avec l'API Imdb pour récupérer des informations
// sur les films et les séries (utilisé par Ponthub)
// Testé par PonthubControllerTest
class ImdbService
{
    protected $curlService;
    protected $baseUrl = 'http://www.omdbapi.com/';

    public function __construct(CurlService $curlService)
    {
        $this->curlService = $curlService;
    }

    public function search($name)
    {
        $url = $this->baseUrl . '?s=' . urlencode($name);
        $response = json_decode($this->curlService->curl($url), true);

        $return = [];
        if (isset($response['Search'])) {
            foreach ($response['Search'] as $result) {
                $return[] = [
                    'name' => $result['Title'],
                    'year' => $result['Year'],
                    'type' => $result['Type'],
                    'id' => $result['imdbID']
                ];
            }
        }

        return $return;
    }

    public function infos($id)
    {
        $url = $this->baseUrl . '?i=' . urlencode($id);
        $response = json_decode($this->curlService->curl($url), true);

        if (!isset($response['Title']))
            return null;

        if (preg_match('#\d–\d#', $response['Year']))
            $response['Year'] = explode('–', $response['Year'])[0];

        return [
            'title' => $response['Title'],
            'year' => (int)$response['Year'],
            'duration' => 60 * str_replace(' min', '', $response['Runtime']),
            'genres' => explode(', ', $response['Genre']),
            'director' => $response['Director'],
            'actors' => explode(', ', $response['Actors']),
            'description' => $response['Plot'],
            'image' => $response['Poster'],
            'rating' => isset($response['Metascore']) ? $response['Metascore'] : $response['imdbRating'] * 10
        ];
    }
}
