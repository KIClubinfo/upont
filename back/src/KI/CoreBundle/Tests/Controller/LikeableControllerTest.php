<?php

namespace KI\CoreBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

// Tests généraux à toutes les classes
class LikeableControllerTest extends WebTestCase
{
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
}
