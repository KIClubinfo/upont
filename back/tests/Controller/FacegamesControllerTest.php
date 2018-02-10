<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class FacegamesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/facegames', [
                'promo' => '016',
                'hardcore' => true
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $gameId = json_decode($response->getContent(), true)['id'];

        $this->client->request('GET', '/facegames/' . $gameId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request(
            'PATCH', '/facegames/' . $gameId, ['wrongAnswers' => 42, 'duration' => 140]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    // Obligé de faire une seule grosse fonction pour utiliser le meme id
    // Celui-ci change à chaque fois, le auto_increment n'étant pas reset
    // lors du chargement des fixtures
    public function testGet()
    {
        $this->client->request('GET', '/facegames/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/facegames/0', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStatistics()
    {
        $this->client->request('GET', '/statistics/facegame');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/statistics/facegame/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/statistics/facegame/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
