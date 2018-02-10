<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class FoyerControllerTest extends WebTestCase
{
    public function testStatistics()
    {
        $this->client->request('GET', '/statistics/foyer');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/statistics/foyer/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->connect('peluchom', 'password');
        $this->client->request('POST', '/transactions', ['credit' => 20.5, 'user' => 'trancara']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('POST', '/transactions', ['credit' => -20.5, 'user' => 'trancara']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }

    public function testDashboard() {
        $this->client->request('GET', '/statistics/foyer/dashboard');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('promoBalances', $data);
        $this->assertArrayHasKey('soldBeers', $data);

        $this->connect('peluchom', 'password');
        $this->client->request('GET', '/statistics/foyer/dashboard');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->connect('muzardt', 'password');
        $this->client->request('GET', '/statistics/foyer/dashboard');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);
    }

    public function testDebts() {
        $this->client->request('GET', '/foyer/debts');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('application/force-download', $response->headers->get('Content-Type'));

        $this->connect('peluchom', 'password');
        $this->client->request('GET', '/foyer/debts');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->connect('muzardt', 'password');
        $this->client->request('GET', '/foyer/debts');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testPromoBalance() {
        $this->client->request('GET', '/foyer/promo-balance');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('application/force-download', $response->headers->get('Content-Type'));

        $this->connect('peluchom', 'password');
        $this->client->request('GET', '/foyer/promo-balance');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->connect('muzardt', 'password');
        $this->client->request('GET', '/foyer/promo-balance');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }
}
