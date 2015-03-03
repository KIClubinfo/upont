<?php

namespace KI\UpontBundle\Tests\Controller\Publications;

use KI\UpontBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class CoursesControllerTest extends WebTestCase
{
    public function testCoursesParsing()
    {
        $this->client->request('HEAD', '/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 202);
    }

    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/courses', array('name' => 'Mécanique des familles', 'group' => 3, 'semester' => 0, 'startDate' => 151515, 'endDate' => 31415, 'department' => 'GCC'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/courses/mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/courses/qdqddsdwxa');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/own/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testAttend()
    {
        $this->client->request('POST', '/courses/mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/courses/mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('DELETE', '/courses/mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/courses/mecanique-des-familles', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/courses/mecanique-des-familles', array('semester' => 1));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/courses/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }










    // Tests relatifs aux annales
    public function testPostExercice()
    {
        $basePath = __DIR__ . '/../../../../../../web/uploads/tests/';
        $fs = new Filesystem();
        $fs->copy($basePath . 'file.pdf', $basePath . 'file_tmp.pdf');
        $file = new UploadedFile($basePath . 'file_tmp.pdf', 'file.pdf');

        $this->client->request('POST', '/courses/mecanique-des-familles/exercices', array('name' => 'Examen'), array('file' => $file));
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
    }

    public function testDownloadExercice()
    {
        $this->client->request('GET', '/courses/mecanique-des-familles/exercices/examen/download');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/pdf'));
    }

    public function testPatchExercice()
    {
        $this->client->request('PATCH', '/courses/mecanique-des-familles/exercices/examen', array('name' => 'Annale Test'));
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
