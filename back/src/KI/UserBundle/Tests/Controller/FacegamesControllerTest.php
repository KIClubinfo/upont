<?php

namespace KI\UserBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class FacegamesControllerTest extends WebTestCase
{
    protected $gameId;

    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
			'POST', '/facegames', array(
                'promo' => '016',
                'hardcore' => true
			)
		);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    // obligé de faire une seule grosse fonction pour utiliser le meme id
    // Car celui ci change à chaque fois, le auto_incrmeent n'étant pas reset
    // Lors du chargement des fixtures
    public function testCors()
        $this->client->request('GET', '/facegames');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $data = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($data));
        $key = array_keys($data)[0];
        $this->assertTrue(isset($data[$key]['id']));
        $this->gameId = $data[$key]['id'];

        $this->client->request('GET', '/facegames/'.$this->gameId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/facegames/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request(
            'PATCH', '/facegames/'.$this->gameId, array('wrongAnswers' => 42));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/facegames/0', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/facegames/'.$this->gameId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/facegames/'.$this->gameId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
