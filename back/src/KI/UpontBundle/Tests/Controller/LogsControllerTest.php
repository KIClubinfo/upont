<?php

namespace KI\UpontBundle\Tests\Controller;

use KI\UpontBundle\Tests\WebTestCase;

class LogsControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client->request('GET', '/logs');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
