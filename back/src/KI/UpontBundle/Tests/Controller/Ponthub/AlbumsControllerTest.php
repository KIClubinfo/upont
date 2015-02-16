<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;

class AlbumsControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/ponthub/albums');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/albums/back-in-black');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/albums/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testPatch()
    {
        $this->client->request('PATCH', '/ponthub/albums/black-album', array('year' => 1003));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/ponthub/albums/back-in-black', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/ponthub/albums/sjoajsiohaysahaiasbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testLike()
    {
        $this->client->request('GET', '/ponthub/albums/basdsqdqsdqck-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/unkike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('POST', '/ponthub/albums/back-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('POST', '/ponthub/albums/back-in-black/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/albums/back-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/albums/back-in-black/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    
    // Relatif aux musiques en elles-mêmes
    
    public function testGetMusic()
    {
        $this->client->request('GET', '/ponthub/albums/back-in-black/musics');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/albums/sjoajsiohaysahais-asbsksaba7/musics');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/musics/giving-the-dog-a-bone');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
    
    public function testPatchMusic()
    {
        $this->client->request('PATCH', '/ponthub/albums/back-in-black/musics/giving-the-dog-a-bone', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
        
        $this->client->request('PATCH', '/ponthub/albums/back-in-black/musics/sqdssdqfvsdgr');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testStats()
    {
        $this->client->request('GET', '/ponthub/albums/back-in-black/musics/giving-the-dog-a-bone/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/musics/giving-the-dog-a-bone/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/albums/back-in-black/musics/giving-the-dog-a-bone');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
}
