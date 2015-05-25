<?php

namespace KI\UpontBundle\Tests\Controller\Foyer;

use KI\UpontBundle\Tests\WebTestCase;

class YoutubesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/youtubes', array(
                'name' => 'Test Youtube',
                'link' => 'www.youtube.com/watch?v=dQw4w9WgXcQ'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/youtubes');
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
