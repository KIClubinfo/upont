<?php

namespace KI\UserBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class PromoControllerTest extends WebTestCase
{
    public function testPromoGame()
    {
        $this->client->request('GET', '/promo/016/game');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
