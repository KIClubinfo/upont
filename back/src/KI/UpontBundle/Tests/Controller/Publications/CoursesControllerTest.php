<?php

namespace KI\UpontBundle\Tests\Controller\Publications;

use KI\UpontBundle\Tests\WebTestCase;

class CoursesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/courses', array('name' => 'Mécanique des familles', 'semester' => 0, 'startDate' => 151515, 'endDate' => 31415, 'department' => 'GCC'));
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

        $this->client->request('GET', '/courses/gcc-mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/courses/qdqddsdwxa');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/own/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetOwn()
    {
        $this->client->request('GET', '/own/coursesitems');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testAttend()
    {
        $this->client->request('POST', '/courses/gcc-mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/courses/gcc-mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('DELETE', '/courses/gcc-mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/gcc-mecanique-des-familles/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/courses/gcc-mecanique-des-familles', array('semester' => 1));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/courses/gcc-mecanique-des-familles', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/courses/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/courses/gcc-mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/gcc-mecanique-des-familles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testCoursesParsing()
    {
        $this->client->request('HEAD', '/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 202);
    }
}
