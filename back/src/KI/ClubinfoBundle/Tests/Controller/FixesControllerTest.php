<?php

namespace KI\PublicationBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class FixesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    // Le problem de ce fix est utilisé dans une condition du hook slack (cf le FixesController)
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/fixes',
            array(
                'name' => 'Panne d\'Internet',
                'problem' => '[Test] J\'arrive pas à avoir Internet',
                'status' => 'En attente',
                'fix' => true
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/fixes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixes/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixes/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/fixes/panne-d-internet',
            array('status' => 'Résolu !')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/fixes/panne-d-internet', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/fixes/sjoajsiosbsksaba7', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/fixes/panne-d-internet', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }
}
