<?php

namespace KI\UserBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class ClubsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST', '/clubs', array(
                'fullName' => 'Chasse Ponts Tradition',
                'name' => 'CPT',
                'administration' => true,
                'assos' => false,
                'presentation' => 'La liste pipeau'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/clubs');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/clubs/cpt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/clubs/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testGetPublications()
    {
        $this->client->request('GET', '/clubs/cpt/events');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/clubs/cpt/newsitems');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH', '/clubs/cpt', array(
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png',
                'banner' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/clubs/cpt', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/clubs/sjoajsiosbsksaba7', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/clubs/cpt', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }



    // Tests relatifs aux membres

    public function testGetUser()
    {
        $this->client->request('GET', '/clubs/cpt/users');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testLink()
    {
        $this->client->request('POST', '/clubs/cpt/users/dziris', array('role' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/clubs/cpt/users/dziris', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/cpt/users/trancara', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/cpt/users/dziris', array('role' => 'Test 2'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/clubs/fdxcyhjbj/users/dziris', array('role' => 'Test 2'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testLinkEdit()
    {
        $this->client->request('PATCH', '/clubs/cpt/users/dziris', array('role' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/clubs/cpt/users/dziris', array('role' => 'Test 33'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testUnlink()
    {
        $this->client->request('DELETE', '/clubs/cpt/users/dziriqsqsqsss');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/clubs/ksdsddssdi/users/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/clubs/cpt/users/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testUnFollow() {
        $this->client->request('POST', '/clubs/cpt/unfollow');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/dqflkabereich/unfollow');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testFollow() {
        $this->client->request('POST', '/clubs/cpt/follow');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/dqflkabereich/follow');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/clubs/cpt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/clubs/cpt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
