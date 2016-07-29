<?php

namespace Tests\KI\CoreBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class DefaultControllerTest extends WebTestCase
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
        $client = static::createClient();
        $client->request('POST', '/login', array('username' => 'user', 'password' => 'userwrongpass'));
        $this->assertJsonResponse($client->getResponse(), 401, true);
    }

    public function testLoginSuccess()
    {
        $client = static::createClient();
        $client->request('POST', '/login', array('username' => 'trancara', 'password' => 'password'));

        $this->assertJsonResponse($client->getResponse(), 200, true);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('data', $response);

        // On vérifie que le token de la requête marche bien
        $client = static::createClient();
        $client->request('HEAD', $this->getUrl('ping', array($this->queryParameterName => $response['token'])));
        $this->assertJsonResponse($client->getResponse(), 204);

        // On vérifie que le token reçu marche bien
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token']));
        $client->request('HEAD', '/ping');
        $this->assertJsonResponse($client->getResponse(), 204);

        // On vérifie que le token reçu marche plusieurs fois tant qu'il est valide
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token']));
        $client->request('HEAD', '/ping');
        $this->assertJsonResponse($client->getResponse(), 204);

        // On vérifie qu'un mauvais token ne marche pas
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token'].'changed'));
        $client->request('GET', '/movies');
        $this->assertJsonResponse($client->getResponse(), 401);

        // On vérifie qu'une erreur est retournée si l'on ne précise pas le header d'autorisation
        $client = static::createClient();
        $client->request('GET', '/movies');
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testMaintenance()
    {
        $this->client->request('DELETE', '/maintenance');
        $this->assertJsonResponse($this->client->getResponse(), 400);

        $this->client->request('POST', '/maintenance', array('until' => time()));
        $this->assertJsonResponse($this->client->getResponse(), 204);

        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 503);

        $this->client->request('DELETE', '/maintenance');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);
    }

    public function testPing()
    {
        $this->client->request('HEAD', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 204);

        $this->client->request('GET', '/ping');
        $this->assertJsonResponse($this->client->getResponse(), 405);

        $client = static::createClient();
        $client->request('HEAD', '/ping');
        $this->assertJsonResponse($client->getResponse(), 204);
    }

    public function testSearch()
    {
        $this->client->request('POST', '/search', array('search' => 'User/al'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('POST', '/search', array('search' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Users/'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'al'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Miam/'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Miam/ps'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testVersion()
    {
        $this->client->request('GET', '/version');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('version', $response);
        $this->assertArrayHasKey('major', $response);
        $this->assertArrayHasKey('minor', $response);
        $this->assertArrayHasKey('build', $response);
    }
}
