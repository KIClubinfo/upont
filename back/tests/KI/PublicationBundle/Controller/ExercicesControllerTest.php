<?php

namespace Tests\KI\PublicationBundle\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\KI\CoreBundle\WebTestCase;

class ExercicesControllerTest extends WebTestCase
{
    // Tests relatifs aux annales
    public function testPostExercice()
    {
        $basePath = __DIR__.'/../../../../web/uploads/tests/';
        $fs = new Filesystem();
        $fs->copy($basePath.'file.pdf', $basePath.'file_tmp.pdf');
        $file = new UploadedFile($basePath.'file_tmp.pdf', 'file.pdf');

        $this->client->request(
            'POST',
            '/courses/mecanique-des-familles/exercices',
            array('name' => 'Super Examen'),
            array('file' => $file)
            );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGetExercice()
    {
        $this->client->request('GET', '/courses/mecanique-des-familles/exercices');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/courses/mecanique-des-familles/exercices/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/courses/mecanique-des-familles/exercices/super-examen');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testDownloadExercice()
    {
        ob_start();
        $this->client->request('GET', '/courses/mecanique-des-familles/exercices/super-examen/download');
        $response = $this->client->getResponse();
        ob_end_clean();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/pdf'));
    }

    public function testPatchExercice()
    {
        $this->client->request('PATCH', '/courses/mecanique-des-familles/exercices/super-examen', array('name' => 'Annale Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/courses/mecanique-des-familles/exercices/annale-test', array('uploader' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/courses/mecanique-des-familles/exercices/sjoajsiosbsksaba7', array('name' => 'Test', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    // On supprime tout
    public function testDeleteExercice()
    {
        $this->client->request('DELETE', '/courses/mecanique-des-familles/exercices/annale-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/mecanique-des-familles/exercices/annale-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/courses/mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
