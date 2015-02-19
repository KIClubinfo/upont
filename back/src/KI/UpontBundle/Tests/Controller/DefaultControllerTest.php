<?php

namespace KI\UpontBundle\Tests\Controller;

use KI\UpontBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
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
        $client->request('HEAD', $this->getUrl('upont_api_ping', array($this->queryParameterName => $response['token'])));
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
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $response['token'] . 'changed'));
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

    public function testOnline()
    {
        $this->client->request('GET', '/online');
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $this->client->request('GET', '/online?delay=5');
        $this->assertJsonResponse($this->client->getResponse(), 200);
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

    public function testRequestResetting()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', '/resetting/request', array('username' => 'iqhjioqiosois'));

        // On vérifie que l'email a été envoyé
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(0, $mailCollector->getMessageCount());
        $this->assertJsonResponse($client->getResponse(), 404);

        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', '/resetting/request', array('username' => 'trancara'));

        // On vérifie que l'email a été envoyé
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $this->assertJsonResponse($client->getResponse(), 204);

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // On vérifie le message
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Réinitialisation du mot de passe', $message->getSubject());
        $this->assertEquals('noreply@upont.enpc.fr', key($message->getFrom()));
        $this->assertEquals('alberic.trancart@eleves.enpc.fr', key($message->getTo()));

        // On récupère le token
        $this->assertTrue(preg_match('#/reset/(.*)\n\n.*Si#is', $message->getBody(), $token) == 1);
        $token = $token[1];

        // On teste le reset en lui même
        $this->client->request('POST', '/resetting/token/dfdsdsfdsfsfds', array('password' => '1234', 'check' => '1234'));
        $this->assertJsonResponse($this->client->getResponse(), 404);

        $this->client->request('POST', '/resetting/token/' . $token, array('password' => 'password', 'check' => '12sdqsdsqdqds34'));
        $this->assertJsonResponse($this->client->getResponse(), 400);

        $this->client->request('POST', '/resetting/token/' . $token, array('password' => 'password', 'check' => 'password'));
        $this->assertJsonResponse($this->client->getResponse(), 204);
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
        $this->assertArrayHasKey('environment', $response);
    }
}
