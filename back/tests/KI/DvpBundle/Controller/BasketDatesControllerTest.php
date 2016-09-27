<?php

namespace Tests\KI\DvpBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class BasketDatesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/basketdates', [
                'locked' => false,
                'dateRetrieve' => date("Y-m-d", mt_rand(1, 2147385600))
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        return json_decode($response->getContent(), true)['id'];
    }

    /**
     * @depends testPost
     */
    public function testGet($id)
    {
        $this->client->request('GET', '/basketdates');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/basketdates/' . $id);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/basketdates/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    /**
     * @depends testPost
     */
    public function testPatch($id)
    {
        $this->client->request('PATCH', '/basketdates/' . $id, [
                'locked' => true,
            ]
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/basketdates/' . $id, ['dateRetrieve' => '3 janvier']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/basketdates/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    /**
     * @depends testPost
     */
    public function testDelete($id)
    {
        $this->client->request('DELETE', '/basketdates/' . $id);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/basketdates/' . $id);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
