<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

// Tests généraux à toutes les classes
class BaseControllerTest extends WebTestCase
{
    public function testPagination()
    {
        $this->client->request('GET', '/clubs?page=1&limit=10&sort=-fullName');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testFilter()
    {
        $this->client->request('GET', '/courses/mecanique-des-structures/exercices?department=1A&sort=semester,-startDate');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
