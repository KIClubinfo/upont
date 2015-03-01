<?php

namespace KI\UpontBundle\Tests\Controller\Users;

use KI\UpontBundle\Tests\WebTestCase;

class PromoGameControllerTest extends WebTestCase
{
    public function testPromoGame()
    {
        $this->client->request('GET', '/promogame');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
