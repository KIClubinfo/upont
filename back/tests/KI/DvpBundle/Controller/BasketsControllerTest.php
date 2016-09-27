<?php

namespace Tests\KI\DvpBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class BasketsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/baskets', [
            'name' => 'Panier test',
            'content' => 'Des fruits, des légumes... que des bonnes choses',
            'price' => 10
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }

    public function testGet()
    {
        $this->client->request('GET', '/baskets');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/baskets/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/baskets/panier-test', [
                'price' => 20,
                'content' => 'Encore plus de bonnes choses'
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/baskets/panier-test', ['text' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/baskets/sjoajsiohaysahais-asbsksaba7', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
