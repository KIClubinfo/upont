<?php

namespace KI\PublicationBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class CommandesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/centrales/cles-usb/commandes',
            array(
                'paid' => false,
                'quantity' => 2,
                'taken' => true,
                )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);

        $this->client->request(
            'POST',
            '/centrales/cles-usb/commandes',
            array(
                'paid' => false,
                'quantity' => 2,
                'taken' => true,
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testGet()
    {
        $this->client->request('GET', '/centrales/cles-usb/commandes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/centrales/cles-usb/commandes/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/centrales/sjoajsiohayahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/centrales/cles-usb/commandes/trancara',
            array('paid' => true)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/centrales/cles-usb/commandes/trancara', array('quantitytxtghxgfrth' => 2));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/centrales/sjoajsiosbsksaba7', array('quantity' => 2, 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/centrales/cles-usb/commandes/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/centrales/cles-usb/commandes/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
