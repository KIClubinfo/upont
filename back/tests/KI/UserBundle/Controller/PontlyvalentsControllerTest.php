<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class PontlyvalentsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/users/taquet-c/pontlyvalent', [
                'text' => 'Meilleure successeur possible <3'
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        // $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/users/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/taquet-c/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/taquet-c/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH', '/users/taquet-c/pontlyvalent', [
            'text' => 'Aime les câlins <3'
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request(
            'PATCH', '/users/taquet-c/pontlyvalent', ['text' => '']
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/users/taquet-c/sjoajsiosbsksaba7', ['name' => 'miam', 'mail' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/users/taquet-c/pontlyvalent', ['name' => 'miam', 'mail' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/users/taquet-c/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/users/taquet-c/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
