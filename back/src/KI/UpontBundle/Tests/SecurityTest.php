<?php

namespace KI\UpontBundle\Tests;

use KI\UpontBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $client = static::createClient();

        $routes = array(
            array('GET',    200, '/clubs'),
            array('GET',    401, '/newsitems'),
            array('GET',    200, '/clubs/ki'),
            array('GET',    404, '/clubs/sddsdqs'),
            array('GET',    404, '/courses/mecanique-des-structures/exercices/test/download'),
            array('POST',   401, '/clubs'),
            array('POST',   400, '/resetting/request'),
            array('PATCH',  401, '/promo/016/pictures'),
        );

        foreach($routes as $route) {
            $client->request($route[0], $route[2]);
            $this->assertJsonResponse($client->getResponse(), $route[1]);
        }
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $client = static::createClient();
        $client->request('POST', $this->getUrl('login'), array('username' => 'donat-bb', 'password' => 'password'));
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $client = static::createClient();
        $this->assertArrayHasKey('token', $data);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;

        // Maintenant on teste quelques trucs
        $this->client->request('POST', '/newsitems', array('title' => 'La Porte', 'textLong' => 'C\'est comme perdre', 'authorClub' => 'bde'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('DELETE', '/newsitems/le-jeu');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('POST', '/newsitems', array('name' => 'Manger', 'textLong' => 'C\'est comme perdre', 'authorClub' => 'bda'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('DELETE', '/newsitems/manger');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }
}
