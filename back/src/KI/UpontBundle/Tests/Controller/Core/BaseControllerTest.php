<?php

namespace KI\UpontBundle\Tests\Controller\Core;

use KI\UpontBundle\Tests\WebTestCase;

// Tests généraux à toutes les classes
class BaseControllerTest extends WebTestCase
{
    public function testPagination()
    {
        $this->client->request('GET', '/clubs?page=1&limit=10&sort=-fullName');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->assertTrue($response->headers->has('Links'), $response->headers);
        $this->assertTrue($response->headers->has('Total-count'), $response->headers);
    }

    public function testFilter()
    {
        $this->client->request('GET', '/courses/mecanique-des-structures/exercices?filterBy=department&filterValue=1A');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testLike()
    {
        $this->client->request('POST', '/newsitems/le-jeu/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/newsitems/le-jeu');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('like', $response);
        $this->assertArrayHasKey('dislike', $response);
        $this->assertTrue($response['like']);
        $this->assertTrue(!$response['dislike']);

        $this->client->request('POST', '/newsitems/le-jeu/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/newsitems/le-jeu');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('like', $response);
        $this->assertArrayHasKey('dislike', $response);
        $this->assertTrue(!$response['like']);
        $this->assertTrue($response['dislike']);

        $this->client->request('DELETE', '/newsitems/le-jeu/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/newsitems/le-jeu/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/courses/mecanique-des-structures/exercices/final-016/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/courses/mecanique-des-structures/exercices/final-016/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/albums/black-album/musics/enter-sandman/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/albums/black-album/musics/enter-sandman/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

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
        $url = preg_match('#.*/comments/([0-9]+)$#', $response->headers->get('Location'), $out);
        $this->assertTrue(!empty($out));

        $this->client->request('PATCH', '/comments/' . $out[1], array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/comments/' . $out[1], array('text' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/comments/qsdqdsq', array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/comments/' . $out[1] . '/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/' . $out[1] . '/like');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/comments/' . $out[1] . '/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/' . $out[1] . '/dislike');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/' . $out[1], array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/comments/sqdqsdqs', array('text' => 'J\'ai perdu au Jeu.'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
