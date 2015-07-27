<?php

namespace KI\UpontBundle\Tests\Controller\Users;

use KI\UpontBundle\Tests\WebTestCase;

class FacegamesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
			'POST', '/facegames', array(
                'promo' => '',
                'hardcore' => true
			)
		);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);

    //     $this->client->request(
    //         'POST', '/facegames', array(
    //             'promo' => '',
    //             'hardcore' => false
    //         )
    //     );
    //     $response = $this->client->getResponse();
    //     $this->assertJsonResponse($response, 201);
    }

    public function testGet()
    {
        $this->client->request('GET', '/facegames');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/facegames/1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/facegames/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH', '/facegames/1', array('duration' => 42));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/facegames/1', array('id' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/facegames/0', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/facegames/1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/facegames/1');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        // $this->client->request('DELETE', '/facegames/2');
        // $response = $this->client->getResponse();
        // $this->assertJsonResponse($response, 204);
    }
}
