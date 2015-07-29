<?php

namespace KI\CoreBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class ImagesControllerTest extends WebTestCase
{
    public function testImagePost()
    {
        $basePath = __DIR__.'/../../../../../web/uploads/tests/';
        $fs = new Filesystem();
        $fs->copy($basePath.'admissibles.png', $basePath.'admissibles2.png');
        $list = new UploadedFile($basePath.'admissibles2.png', 'admissibles2.png');

        $this->client->request('POST', '/images', array(), array('file' => $list));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('filelink', $response);
    }

    // public function testCreateThumbnails()
    // {
    //     $this->client->request('POST', '/thumbnails', array('path' => '/web/uploads/images'));
    //     $response = $this->client->getResponse();
    //     $this->assertJsonResponse($response, 201);
    // }


}