<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

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

        $comment = json_decode($response->getContent(), true);

        $this->assertTrue(isset($comment['id']) && !empty($comment['id']));
        $id = $comment['id'];

        $this->client->request('PATCH', '/comments/'.$id, ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/comments/'.$id, ['text' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/comments/qsdqdsq', ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/comments/'.$id.'/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$id.'/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/comments/'.$id.'/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$id.'/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/'.$id, ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/sqdqsdqs', ['text' => 'J\'ai perdu au Jeu.']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
