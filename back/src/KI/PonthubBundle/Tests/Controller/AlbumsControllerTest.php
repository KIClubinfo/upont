<?php

namespace KI\PonthubBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class AlbumsControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/albums');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/albums/back-in-black');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/albums/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/albums/black-album', array('image' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png', 'year' => 1003));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/albums/back-in-black', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/albums/sjoajsiohaysahaiasbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    // Relatif aux musiques en elles-mêmes

    public function testGetMusic()
    {
        $this->client->request('GET', '/albums/back-in-black/musics');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/albums/sjoajsiohaysahais-asbsksaba7/musics');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/albums/back-in-black/musics/giving-the-dog-a-bone');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testPatchMusic()
    {
        $this->client->request('PATCH', '/albums/back-in-black/musics/giving-the-dog-a-bone', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/albums/back-in-black/musics/sqdssdqfvsdgr');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/albums/back-in-black/musics/giving-the-dog-a-bone/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/albums/back-in-black/musics/giving-the-dog-a-bone/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/albums/back-in-black/musics/giving-the-dog-a-bone');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
}
