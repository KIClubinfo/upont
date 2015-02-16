<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;

class OthersControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/ponthub/others');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/others/windows-vista');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/ponthub/others/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testPatch()
    {
        $this->client->request('PATCH', '/ponthub/others/windows-vista', array('description' => 'De la daube...', 'tags' => array(array('name' => 'windaube'), array('name' => 'vista'))));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/ponthub/others/windows-vista', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/ponthub/others/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
    
    public function testStats()
    {
        $this->client->request('GET', '/ponthub/others/windows-vista/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/others/windows-vista/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);
        
        $this->client->request('GET', '/ponthub/others/windows-vista');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
    
    public function testLike()
    {
        $this->client->request('GET', '/ponthub/others/basdsqdqsdqck-in-black/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/others/windows-vista/unkike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
        
        $this->client->request('GET', '/ponthub/others/windows-vista/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('GET', '/ponthub/others/windows-vista/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $this->client->request('POST', '/ponthub/others/windows-vista/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('POST', '/ponthub/others/windows-vista/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/others/windows-vista/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        
        $this->client->request('DELETE', '/ponthub/others/windows-vista/unlike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }
}
