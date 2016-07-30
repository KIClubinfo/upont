<?php

namespace Tests\KI\CoreBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

// Tests généraux à toutes les classes
class CommentsControllerTest extends WebTestCase
{
    public function testComments()
    {
        $this->client->request('GET', '/newsitems/le-jeu/comments');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('POST', '/newsitems/le-jeu/comments');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/newsitems/le-jeu/comments', ['text' => 'J\'ai perdu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $out = [];
        preg_match('#.*/comments/([0-9]+)$#', $response->headers->get('Location'), $out);
        $this->assertTrue(!empty($out));

        $this->client->request('PATCH', '/comments/'.$out[1], ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/comments/'.$out[1], ['text' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/comments/qsdqdsq', ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/comments/'.$out[1].'/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$out[1].'/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/comments/'.$out[1].'/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$out[1].'/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$out[1], ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/sqdqsdqs', ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
