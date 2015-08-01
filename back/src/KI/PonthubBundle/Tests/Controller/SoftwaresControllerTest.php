<?php

namespace KI\PonthubBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class SoftwaresControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/softwares');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/softwares/windows-vista-1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/softwares/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/softwares/windows-vista-1', array('image' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png', 'year' => 1999, 'author' => 'Microsoft', 'version' => '0.0.1'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/softwares/windows-vista-1', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/softwares/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/softwares/windows-vista-1/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/softwares/windows-vista-1/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/softwares/windows-vista-1');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
}
