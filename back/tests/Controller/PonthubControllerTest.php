<?php

namespace Tests\App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Tests\WebTestCase;

class PonthubControllerTest extends WebTestCase
{
    public function testFilelist()
    {
        $basePath = __DIR__.'/../uploads/';
        $fs = new Filesystem();
        $fs->copy($basePath.'files_tmp.list', $basePath.'files.list');
        $list = new UploadedFile($basePath.'files.list', 'files.list');

        $this->client->request('POST', '/filelist/12345', [], ['filelist' => $list]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('POST', '/filelist/1234', [], ['filelist' => $list]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 202);

        // On vérifie que les ressources concernées ont bien été créées
        $this->client->request('GET', '/games/dawn-of-war-1-dark-crusade');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/games/europa-universalis-1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/the-chronicles-of-narnia-the-lion-the-witch-and-the-wardrobe');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/movies/the-kings-speech');
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

     public function testImdbSearch()
     {
         $this->client->request('POST', '/imdb/search', ['album' => 'Back In Black']);
         $response = $this->client->getResponse();
         $this->assertJsonResponse($response, 400);

         $this->client->request('POST', '/imdb/search', ['name' => 'Star Wars']);
         $response = $this->client->getResponse();
         $this->assertJsonResponse($response, 200);
         $this->assertTrue(!empty(json_decode($response->getContent())));
     }

     public function testImdbInfos()
     {
         $this->client->request('POST', '/imdb/infos', ['album' => 'Back In Black']);
         $response = $this->client->getResponse();
         $this->assertJsonResponse($response, 400);

         $this->client->request('POST', '/imdb/infos', ['id' => 'tt0076759']);
         $response = $this->client->getResponse();
         $this->assertJsonResponse($response, 200);
         $infos = json_decode($response->getContent(), true);
         $this->assertNotEquals($infos, null);
         $this->assertEquals($infos['year'], 1977);
         $this->assertEquals($infos['duration'], 121*60);
         $this->assertEquals($infos['director'], 'George Lucas');
     }

    public function testGlobalStatistics()
    {
        $this->client->request('GET', '/statistics/ponthub');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }


    public function testUserStatistics()
    {
        $this->client->request('GET', '/statistics/ponthub/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
