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
            '/commandes',
            array(
                'quantity' => 'Panne d\'Internet',
                'payed' => false,
                'taken' => true,
                'centrale' => $this->getReference('centrale-cles-usb'),
                'user' => $this->getReference('user-trancara')
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/commandes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/commandes/cles-usb/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/commandes/sjoajsiohaysahais-asbsksaba7/liuhgvcdfgh');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/commandes/cles-usb/trancara',
            array('payed' => true)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/commandes/cles-usb/trancara', array('quantityt' => 2));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/commandes/sjoajsiosbsksaba7/liuhgvcdfgh', array('quantity' => 2, 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/commandes/cles-usb/trancara', array('quantity' => 2, 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/commandes/cles-usb/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    
        $this->client->request('DELETE', '/commandes/cles-usb/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
