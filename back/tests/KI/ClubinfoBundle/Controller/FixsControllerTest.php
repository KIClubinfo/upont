<?php

namespace Tests\KI\ClubinfoBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class FixsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/fixs',
            [
                'name' => 'Panne d\'Internet',
                'problem' => '[Test] J\'arrive pas à avoir Internet',
                'status' => 'En attente',
                'fix' => true
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/fixs');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixs/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixs/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/fixs/panne-d-internet',
            ['status' => 'Résolu !']
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/fixs/panne-d-internet', ['name' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/fixs/sjoajsiosbsksaba7', ['name' => 'miam', 'mail' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/fixs/panne-d-internet', ['name' => 'miam', 'mail' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/fixs/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/fixs/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
