<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;

class GamesControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/ponthub/games');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/games/age-of-empires-2');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/games/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testPatch()
    {
        $this->client->request('PATCH', '/ponthub/games/age-of-empires-2', array('genres' => array(array('name' => 'Geekage'), array('name' => 'Lanage')), 'year' => 1999));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/ponthub/games/age-of-empires-2', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/ponthub/games/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testStats()
    {
        $this->client->request('GET', '/ponthub/games/age-of-empires-2/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/games/age-of-empires-2/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/games/age-of-empires-2');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
    
    public function testLike()
    {
        $this->client->request('GET', '/ponthub/games/basdsqdqsdqck-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/games/age-of-empires-2/unkike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/games/age-of-empires-2/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('GET', '/ponthub/games/age-of-empires-2/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('POST', '/ponthub/games/age-of-empires-2/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('POST', '/ponthub/games/age-of-empires-2/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/games/age-of-empires-2/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/games/age-of-empires-2/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }
}
