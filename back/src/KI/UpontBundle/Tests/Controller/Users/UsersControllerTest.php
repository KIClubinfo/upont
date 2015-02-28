<?php

namespace KI\UpontBundle\Tests\Controller\Users;

use KI\UpontBundle\Tests\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    protected function postUser()
    {
        $this->client->request(
            'POST',
            '/users',
            array(
                'username' => 'testificate',
                'email' => 'testificate@phpunit.zorg',
                'plainPassword' => array('first' => 'test1234', 'second' => 'test1234'),
                'firstName' => 'KI',
                'lastName' => 'OP',
                'nickname' => 'Testeur en chef'
            )
        );
        return $this->client->getResponse();
    }

    public function testPost()
    {
        $response = $this->postUser();
        $this->assertJsonResponse($response, 201);

        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue(
            $response->headers->has('Location'),
            $response->headers
        );

        // On n'accepte pas les duplicatas selon l'username
        $response = $this->postUser();
        $this->assertJsonResponse($response, 400);
    }

    public function testGet()
    {
        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/testificate');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/users', array('username' => '', 'email' => '123'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/users/testificate', array('firstName' => 'KIMiam', 'gender' => 'M', 'phone' => '06.45.03.69.58'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/users/testificate', array('firstName' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/users/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDeleteFail()
    {
        $this->client->request('DELETE', '/users/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/users/testificate');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testGetUserClubs()
    {
        $this->client->request('GET', '/users/trancara/clubs');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testCalendar()
    {
        $this->client->request('GET', '/users/4wtyfMWp/calendar');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
