<?php

namespace Tests\App\Controller;

use App\Tests\WebTestCase;

class CoreControllerTest extends WebTestCase
{
    public function testCleaning()
    {
        $this->client->request('GET', '/clean');
        $this->assertJsonResponse($this->client->getResponse(), 204);
    }

    public function testDirty()
    {
        $this->client->request('GET', '/dirty');
        $this->assertJsonResponse($this->client->getResponse(), 302);
    }

    public function testLoginFailure()
    {
        $this->asAnon();
        $this->client->request('POST', '/login', ['username' => 'user', 'password' => 'userwrongpass']);
        $this->assertJsonResponse($this->client->getResponse(), 401, true);
    }

    public function testLoginSuccess()
    {
        $this->asAnon();
        $this->client->request('POST', '/login', ['username' => 'trancara', 'password' => 'password']);

        $this->assertJsonResponse($this->client->getResponse(), 200, true);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('data', $response);

        // On vérifie que le token de la requête marche bien
        $this->asAnon();
        $this->client->request('HEAD', '/ping?' . $this->queryParameterName . '=' . $response['token']);
        $this->assertJsonResponse($this->client->getResponse(), 204);

        // On vérifie que le token reçu marche bien
        $this->asAnon();
        $this->client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token']));
        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        // On vérifie que le token reçu marche plusieurs fois tant qu'il est valide
        $this->asAnon();
        $this->client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token']));
        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        // On vérifie qu'un mauvais token ne marche pas
        $this->asAnon();
        $this->client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token'].'changed'));
        $this->client->request('GET', '/movies');
        $this->assertJsonResponse($this->client->getResponse(), 401);

        // On vérifie qu'une erreur est retournée si l'on ne précise pas le header d'autorisation
        $this->asAnon();
        $this->client->request('GET', '/movies');
        $this->assertJsonResponse($this->client->getResponse(), 401);
    }

    public function testPing()
    {
        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        $this->client->request('GET', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        $this->asAnon();
        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);
    }

    public function testSearch()
    {
        $this->client->request('POST', '/search', ['search' => 'User/al']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('POST', '/search', ['search' => '']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', ['search' => 'Users/']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', ['search' => 'al']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', ['search' => 'Miam/']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', ['search' => 'Miam/ps']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testVersion()
    {
        $this->client->request('GET', '/version');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('major', $response);
        $this->assertArrayHasKey('minor', $response);
        $this->assertArrayHasKey('patch', $response);
        $this->assertArrayHasKey('hash', $response);
    }
}
