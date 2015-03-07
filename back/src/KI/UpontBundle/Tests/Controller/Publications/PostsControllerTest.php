<?php

namespace KI\UpontBundle\Tests\Controller\Publications;

use KI\UpontBundle\Tests\WebTestCase;

class PostsControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/posts');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
