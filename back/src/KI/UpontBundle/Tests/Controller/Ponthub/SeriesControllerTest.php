<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;

class SeriesControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/series');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/how-i-met-your-mother');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/series/how-i-met-your-mother', array(
            'year' => '2004',
            'duration' => 7800,
            'vf' => true,
            'vost' => true,
            'vostfr' => false,
            'director' => 'Mickael Bay',
            'actors' => array(array('name' => 'Josh Radnor')),
            'genres' => array(array('name' => 'SitCom')),
            'rating' => 42
        ));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/series/how-i-met-your-mother', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/series/sjoajsiohaysahaiasbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    // Relatif aux épisodes

    public function testGetEpisode()
    {
        $this->client->request('GET', '/series/how-i-met-your-mother/episodes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/sjoajsiohaysahais-asbsksaba7/episodes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testPatchEpisode()
    {
        $this->client->request('PATCH', '/series/how-i-met-your-mother/episodes/pilot', array('name' => 'PiLoT'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/series/how-i-met-your-mother/episodes/pilot', array('size' => 0));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/series/dsddddddddddvavzaza/episodes/pilot', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }
}
