<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

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
        $this->client->request('PATCH', '/series/how-i-met-your-mother', [
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png',
            'year' => '2004',
            'duration' => 7800,
            'director' => 'Mickael Bay',
            'actors' => 'Josh Radnor',
            'genres' => 'SitCom',
            'rating' => 42
        ]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/series/how-i-met-your-mother', ['size' => 0]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/series/sjoajsiohaysahaiasbsksaba7', ['username' => 'miam', 'email' => '123@mail.fr']);
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
        $this->client->request('PATCH', '/series/how-i-met-your-mother/episodes/pilot', ['name' => 'PiLoT']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/series/how-i-met-your-mother/episodes/pilot', ['size' => 0]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/series/dsddddddddddvavzaza/episodes/pilot', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testStats()
    {
        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot/download');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 302);

        $this->client->request('GET', '/series/how-i-met-your-mother/episodes/pilot');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['downloads'], 1);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/series/how-i-met-your-mother');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/series/sjoajsiohaysahaiasbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
