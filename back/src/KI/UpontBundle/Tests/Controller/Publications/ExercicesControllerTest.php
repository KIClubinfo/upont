<?php

namespace KI\UpontBundle\Tests\Controller\Publications;

use KI\UpontBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ExercicesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $basePath = __DIR__ . '/../../../../../../web/uploads/tmp/';
        $fs = new Filesystem();
        $fs->copy($basePath . 'file.pdf', $basePath . 'file_tmp.pdf');
    	$file = new UploadedFile($basePath . 'file_tmp.pdf', 'file.pdf');

        $this->client->request('POST', '/exercices', array('department' => 'IMI', 'name' => 'Examen'), array('file' => $file));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/exercices');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/exercices/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDownload()
    {
        $this->client->request('GET', '/exercices/examen/download');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/pdf'));
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/exercices/examen', array('name' => 'Annale Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/exercices/annale-test', array('uploader' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/exercices/sjoajsiosbsksaba7', array('name' => 'Test', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/exercices/annale-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/exercices/annale-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
