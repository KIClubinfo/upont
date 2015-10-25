<?php

namespace KI\FoyerBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class TransactionsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testCors()
    {
        $this->connect('peluchom', 'password');
        $this->client->request('POST', '/transactions', array('user' => 'trasqdsqdsqsqdncara', 'beer' => 'lesqddqsqffe'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('POST', '/transactions', array('user' => 'trancara', 'beer' => 'leffe'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('GET', '/transactions');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/userbeers');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/users/trancara/transactions');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $data = json_decode($response->getContent(), true);
        $this->assertTrue(!empty($data));
        $key = array_keys($data)[0];
        $this->assertTrue(isset($data[$key]['id']));
        $transactionId = $data[$key]['id'];

        $this->client->request('DELETE', '/transactions/'.$transactionId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/transactions/'.$transactionId);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
