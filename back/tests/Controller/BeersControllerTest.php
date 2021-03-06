<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class BeersControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/beers', [
                'name' => 'Test Kro',
                'price' => 1,
                'alcohol' => 1,
                'volume' => 1,
                'active' => true,
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data["active"]);
    }

    public function testGet()
    {
        $this->client->request('GET', '/beers');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/beers/test-kro', ['alcohol' => 100]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/beers/test-kro', ['alcohol' => 'blah']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/beers/test-ksdqsdqsdsdqsdsdqsro', ['alcohol' => 'blah']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/beers/test-kro', ['active' => false]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data["active"]);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/beers/test-kro');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/beers/test-kro');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
