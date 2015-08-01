<?php

namespace KI\FoyerBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class BeerUsersControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testCors()
    {
        $this->connect('peluchom', 'password');
        $this->client->request('POST', '/beers/trancara/users/leffe');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/beers/leffe/users/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/beerusers');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/userbeers');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/trancara/beers');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $data = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($data));
        $key = array_keys($data)[0];
        $this->assertTrue(isset($data[$key]['id']));
        $beerUserId = $data[$key]['id'];

        $this->client->request('DELETE', '/beers/leffe/users/trancara/'.$beerUserId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/beers/leffe/users/trancara/'.$beerUserId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
