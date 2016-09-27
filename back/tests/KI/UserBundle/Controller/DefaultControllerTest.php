<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testOnline()
    {
        $this->client->request('GET', '/online');
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $this->client->request('GET', '/online?delay=5');
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    public function testRequestResetting()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', '/resetting/request', ['username' => 'iqhjioqiosois']);

        // On vérifie que l'email a été envoyé
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(0, $mailCollector->getMessageCount());
        $this->assertJsonResponse($client->getResponse(), 404);

        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', '/resetting/request', ['username' => 'trancara']);

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
        $this->assertTrue(!empty($token));

        // On teste le reset en lui même
        $this->client->request('POST', '/resetting/token/dfdsdsfdsfsfds', ['password' => '1234', 'check' => '1234']);
        $this->assertJsonResponse($this->client->getResponse(), 404);

        $this->client->request('POST', '/resetting/token/'.$token, ['password' => 'password', 'check' => '12sdqsdsqdqds34']);
        $this->assertJsonResponse($this->client->getResponse(), 400);

        $this->client->request('POST', '/resetting/token/'.$token, ['password' => 'azerty', 'check' => 'azerty']);
        $this->assertJsonResponse($this->client->getResponse(), 204);

        // On vérifie que le mot de passe a bien été changé
        $client = static::createClient();
        $client->request('POST', '/login', ['username' => 'trancara', 'password' => 'azerty']);
        $this->assertJsonResponse($client->getResponse(), 200, true);

        // On remet l'ancien mot de passe
        $client = static::createClient();
        $client->request('POST', '/resetting/token/'.$token, ['password' => 'password', 'check' => 'password']);
        $this->assertJsonResponse($this->client->getResponse(), 204);
    }
}
