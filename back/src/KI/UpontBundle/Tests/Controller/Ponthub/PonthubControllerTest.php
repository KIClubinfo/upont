<?php

namespace KI\UpontBundle\Tests\Controller\Ponthub;

use KI\UpontBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PonthubControllerTest extends WebTestCase
{
    public function testFilelist()
    {
        $list = new UploadedFile(__DIR__ . '/../../../../../../web/uploads/tests/files.list', 'files.list');
        $this->client->request('POST', '/filelist', array(), array('filelist' => $list));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 202);

        // On vérifie que les ressources concernées ont bien été créées
        $this->client->request('GET', '/albums/black-sabbath/musics/black-sabbath-1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/albums/black-dog-barking/musics/1-10-black-dog-barking');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/games/dawn-of-war-1-dark-crusade');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/games/europa-universalis-1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/the-chronicles-of-narnia-the-lion-the-witch-and-the-wardrobe');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/the-king-s-speech');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/others/google-sketchup-pro-2015');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/softwares/autocad-2014-windows');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/house-of-cards/episodes/s01-e09-chapter-9');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/series/house-of-cards/episodes/s02-e02-chapter-15');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGracenote()
    {
        $this->client->request('POST', '/gracenote', array('album' => 'Back In Black'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('POST', '/gracenote', array('album' => 'Ride The Lightning', 'artist' => 'Metallica'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['year'], 1984);

        $this->client->request('POST', '/gracenote', array('Back In Black'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/gracenote', array('album' => 'dfsdffszaevzev', 'artist' => 'avrzarzqvzqddq'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 500);
    }

    public function testImdbSearch()
    {
        $this->client->request('POST', '/imdb/search', array('album' => 'Back In Black'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/imdb/search', array('name' => 'Star Wars'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->assertTrue(!empty(json_decode($response->getContent())));
    }

    public function testImdbInfos()
    {
        $this->client->request('POST', '/imdb/infos', array('album' => 'Back In Black'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/imdb/infos', array('id' => 'tt0076759'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['year'], 1977);
        $this->assertEquals($infos['duration'], 121*60);
        $this->assertEquals($infos['director'], 'George Lucas');
    }

    public function testSearch()
    {
        $search = array(
            'name' => 'Star',
            'genre' => 'Action',
            'actor' => 'Harrison Ford',
            'artist' => 'George Lucas',
            'yearMin' => 1980,
            'yearMax' => 2000,
            'durationMin' => 600,
            'durationMax' => 6000,
            'sizeMin' => 1000,
            'sizeMax' => 100000000000
        );
        $this->client->request('POST', '/search', $search);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testStatistics()
    {
        $this->client->request('GET', '/statistics');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
