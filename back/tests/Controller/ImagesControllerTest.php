<?php

namespace Tests\App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Tests\WebTestCase;

class ImagesControllerTest extends WebTestCase
{
    public function testImagePost()
    {
        $basePath = __DIR__.'/../uploads/';
        $fs = new Filesystem();
        $fs->copy($basePath.'admissibles.png', $basePath.'admissibles2.png');
        $list = new UploadedFile($basePath.'admissibles2.png', 'admissibles2.png');

        $this->client->request('POST', '/images', [], ['file' => $list]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('filelink', $response);
    }
}
