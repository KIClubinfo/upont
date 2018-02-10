<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class YoutubesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/youtubes', [
                'name' => 'Test Youtube',
                'link' => 'www.youtube.com/watch?v=dQw4w9WgXcQ'
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }

    public function testGetAll()
    {
        $this->client->request('GET', '/youtubes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGet()
    {
        $this->client->request('GET', '/youtubes/nyan-cat');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/youtubes/test-youtube');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/youtubes/test-youtube');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
