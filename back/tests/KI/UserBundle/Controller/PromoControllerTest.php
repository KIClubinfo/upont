<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class PromoControllerTest extends WebTestCase
{
    public function testPromoGame()
    {
        $this->client->request('GET', '/promo/016/game');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }
}
