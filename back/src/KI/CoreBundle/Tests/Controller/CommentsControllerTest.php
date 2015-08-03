<?php

namespace KI\CoreBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

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

        $this->client->request('POST', '/newsitems/le-jeu/comments', array('text' => 'J\'ai perdu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        $this->assertTrue($response->headers->has('Location'));

        $out = array();
        preg_match('#.*/comments/([0-9]+)$#', $response->headers->get('Location'), $out);
        $this->assertTrue(!empty($out));

        $this->client->request('PATCH', '/comments/'.$out[1], array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/comments/'.$out[1], array('text' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/comments/qsdqdsq', array('text' => 'J\'ai perdu au Jeu.'));
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

        $this->client->request('DELETE', '/comments/'.$out[1], array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/sqdqsdqs', array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
