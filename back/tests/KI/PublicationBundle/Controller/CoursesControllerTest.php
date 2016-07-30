<?php

namespace Tests\KI\PublicationBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

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
        $this->client->request(
            'POST',
            '/courses',
            ['name' => 'Mécanique des familles',
                'groups' => [0, 1, 2, 3],
                'semester' => 'Année complète',
                'active' => true,
                'ects' => 3.5,
                'department' => 'GCC']
            );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
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
        $this->client->request('POST', '/courses/mecanique-des-familles/attend', ['group' => 1]);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/courses/mecanique-des-familles/attend', ['group' => 1]);
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
        $this->client->request('PATCH', '/courses/mecanique-des-familles', ['name' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request(
            'PATCH', '/courses/mecanique-des-familles', ['semester' => 1]
            );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/courses/sjoajsiohaysahais-asbsksaba7', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
