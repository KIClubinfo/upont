<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class GroupsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/groups',
            array('name' => 'Groupe test', 'role' => 'ROLE_USER')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/groups');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/groups/groupe-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/groups/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/groups/groupe-test',
            array('role' => 'ROLE_ADMIN'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/groups/groupe-test',
            array('firstName' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/groups/sjoajsiohaysahais-asbsksaba7',
            array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }


    // Tests relatifs aux membres

    public function testGetUser()
    {
        $this->client->request('GET', '/groups/groupe-test/users');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testLink()
    {
        $this->client->request('POST', '/groups/groupe-test/users/taquet-c');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/groups/groupe-test/users/taquet-c');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/groups/fdxcyhjbj/users/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testUnlink()
    {
        $this->client->request('DELETE', '/groups/groupe-test/users/dziriqsqsqsss');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/groups/ksdsddssdi/users/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/groups/groupe-test/users/taquet-c');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/groups/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/groups/groupe-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/groups/groupe-test');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
