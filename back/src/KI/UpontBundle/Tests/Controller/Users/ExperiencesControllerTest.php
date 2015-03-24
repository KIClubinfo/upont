<?php

namespace KI\UpontBundle\Tests\Controller\Users;

use KI\UpontBundle\Tests\WebTestCase;

class ExperiencesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/experiences', array('name' => 'Form finding', 'startDate' => 51561, 'endDate' => 41564556, 'description' => 'Super stage', 'location' => 'Madrid', 'category' => 'Stage Chiant', 'company' => 'Universidad Polytecnica de Madrid'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/experiences');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/experiences/form-finding');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/experiences/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/experiences/form-finding', array('endDate' => 454523));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/experiences/form-finding', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/experiences/sjoajsiosbsksaba7', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/experiences/form-finding', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/experiences/form-finding');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/experiences/form-finding');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
