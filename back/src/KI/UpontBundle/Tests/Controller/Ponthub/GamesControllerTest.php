<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;

class GamesControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/games');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/games/age-of-empires-2');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/games/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/games/age-of-empires-2', array('genres' => array(array('name' => 'Geekage'), array('name' => 'Lanage')), 'year' => 1999));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/games/age-of-empires-2', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/games/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/games/age-of-empires-2/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/games/age-of-empires-2/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/games/age-of-empires-2');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
}
