<?php

namespace Tests\KI\DvpBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class BasketOrdersControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('GET', '/basketdates');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $dateId = json_decode($response->getContent(), true)[0]['id'];

        // Poste une commande
        $this->client->request('POST', '/baskets-orders', [
                'orders' => [
                    [
                        'dateRetrieve' => $dateId,
                        'ordered' => true,
                        'basket' => 'panier-test'
                    ]
                ]
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testGet()
    {
        $this->client->request('GET', '/baskets-orders/alberic.trancart@eleves.enpc.fr');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/baskets-orders');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/baskets-orders/srgsegherge');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
