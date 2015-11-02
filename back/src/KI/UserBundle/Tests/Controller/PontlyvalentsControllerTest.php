<?php

namespace KI\UserBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class PontlyvalentsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/users/taquet-c/pontlyvalent', array(
                'text' => 'Meilleure présidente <3'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->connect('vessairc', 'password');
        $this->client->request(
            'POST', '/users/taquet-c/pontlyvalent', array(
                'text' => 'Meilleure présidente <3'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        // $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->connect('vessairc', 'password');
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
        $this->connect('vessairc', 'password');
        $this->client->request(
            'PATCH', '/users/taquet-c/pontlyvalent', array(
            'text' => 'Aime les câlins <3'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request(
            'PATCH', '/users/taquet-c/pontlyvalent', array('text' => '')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/users/taquet-c/sjoajsiosbsksaba7', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/users/taquet-c/pontlyvalent', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->connect('vessairc', 'password');
        $this->client->request('DELETE', '/users/taquet-c/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/users/taquet-c/pontlyvalent');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
