<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class MoviesControllerTest extends WebTestCase
{
    public function testGetAll()
    {
        $this->client->request('GET', '/movies');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/pumping-iron');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/movies/pumping-iron', [
            'image' => 'https://ia.media-imdb.com/images/M/MV5BMTg2OTIwNTQ2OF5BMl5BanBnXkFtZTcwNTA4NDAwMQ@@._V1_SX300.jpg',
            'actors' => 'Arnold Schwarzenegger',
            'genres' => 'Bodybuilding,Documentaire',
            'year' => 1977,
            'tags' => 'hjihjk'
        ]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/series/how-i-met-your-mother', [
            'image' => 'https://ia.media-imdb.com/images/M/MV5BMTg2OTIwNTQ2OF5BMl5BanBnXkFtZTcwNTA4NDAwMQ@@._V1_SX300.jpg',
            'actors' => 'Arnold Schwarzenegger',
            'genres' => 'Bodybuilding,Documentaire',
            'year' => 1977,
            'tags' => 'hjihjk'
        ]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/movies/pumping-iron', ['size' => 0]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/movies/sjoajsiohaysahais-asbsksaba7', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/movies/pumping-iron/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/movies/pumping-iron/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/movies/pumping-iron');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 3);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/movies/pumping-iron');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/movies/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
