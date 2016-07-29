<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    protected function postUser()
    {
        $this->client->request(
            'POST',
            '/users',
            [
                'email' => 'testificate@eleves.enpc.fr',
                'firstName' => 'KI',
                'lastName' => 'OP',
            ]
        );
        return $this->client->getResponse();
    }

    public function testPost()
    {
        $response = $this->postUser();
        $this->assertJsonResponse($response, 201);

        $this->client->request('POST', '/users', ['username' => '', 'email' => '123']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testGet()
    {
        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/opk');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/users/opk',
            [
                'firstName' => 'KIMiam',
                'gender' => 'M',
                'phone' => '06.45.03.69.58',
                'promo' => '016',
                'department' => 'GCC',
                'skype' => 'megaPseudo',
                'origin' => 'Concours Commun',
                'nationality' => 'France',
                'location' => 'A51',
                'statsPonthub' => false,
                'statsFacegame' => false,
                'tour' => true,
                'image' => 'http://i.imgur.com/QKKfs.png'
            ]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/users/opk', ['firstName' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/users/sjoajsiohaysahais-asbsksaba7', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDeleteFail()
    {
        $this->client->request('DELETE', '/users/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/users/opk');
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
