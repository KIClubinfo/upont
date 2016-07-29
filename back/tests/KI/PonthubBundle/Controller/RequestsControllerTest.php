<?php

namespace Tests\KI\PonthubBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class RequestsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/requests', array('name' => 'Sucker Punch'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/requests');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/requests/sucker-punch');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/requests/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testUpvote()
    {
        $this->client->request('PATCH', '/requests/sucker-punch/upvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/requests/sucker-punch/upvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/requests/sucker-punch/upvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/requests/susqdsqdsq/upvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDownvote()
    {
        $this->client->request('PATCH', '/requests/sucker-punch/downvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/requests/suckeqsdqdsqqr-punch/downvote');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        // Le compte devrait maintenant être à 3 votes
        $this->client->request('GET', '/requests/sucker-punch');
        $response = $this->client->getResponse();
        $infos = json_decode($response->getContent(), true);
        $this->assertJsonResponse($response, 200);
        $this->assertNotEquals($infos, null);
        $this->assertEquals($infos['votes'], 3);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/requests/sucker-punch');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/requests/sucker-punch');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
