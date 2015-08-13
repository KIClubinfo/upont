<?php

namespace KI\DvpBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class BasketsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/dvp/baskets', array(
            'name' => 'Panier test',
            'content' => 'Des fruits, des légumes... que des bonnes choses',
            'price' => 10
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);

        $this->client->request('POST', '/dvp/baskets/panier-test/order', array());
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testGet()
    {
        $this->client->request('GET', '/dvp/baskets');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/dvp/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/dvp/baskets/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatchBasket()
    {
        $this->client->request('PATCH', '/dvp/baskets/panier-test', array(
            'price' => 20,
            'content' => 'Encore plus de bonnes choses'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/dvp/baskets/panier-test', array('text' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/dvp/baskets/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatchOrder()
    {
        $this->client->request(
            'PATCH',
            '/dvp/baskets/panier-test/order/trancara',
            array('paid' => true, 'dateRetrieve' => 12348596)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/dvp/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/dvp/baskets/panier-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
