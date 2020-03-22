<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class TutosControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/tutos',
            [
                'name' => 'Proxy',
                'text' => 'Pour régler le proxy faut aller dans "Réglages Proxy"',
                'icon' => 'Réseau',
            ]
            );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }

    public function testGet()
    {
        $this->client->request('GET', '/tutos');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/tutos/proxy');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/tutos/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/tutos/proxy', ['icon' => 'lowl']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('PATCH', '/tutos/sjoajslj', ['username' => 'miam', 'email' => '123@mail.fr']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/tutos/proxy');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/tutos/proxy');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
