<?php

namespace KI\UpontBundle\Tests\Controller;

use KI\UpontBundle\Tests\WebTestCase;

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
}
