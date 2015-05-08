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
            array('GET', 200, '/clubs'),
            array('GET', 401, '/newsitems'),
            array('GET', 200, '/clubs/ki'),
            array('GET', 404, '/clubs/sddsdqs'),
            array('GET', 404, '/courses/mecanique-des-structures/exercices/test/download'),
            array('GET', 401, '/series/how-i-met-your-mother/episodes/pilot/download'),
            array('GET', 200, '/users/VpqtuEGC/calendar'),
            array('POST', 401, '/clubs'),
            array('POST', 400, '/resetting/request'),
            array('PATCH', 401, '/promo/016/pictures'),
        );

        foreach ($routes as $route) {
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
        $this->assertArrayHasKey('token', $data);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;

        // Maintenant on teste quelques trucs
        $this->client->request('POST', '/newsitems', array('name' => 'La Porte', 'text' => 'C\'est comme perdre'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('DELETE', '/newsitems/la-porte');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/newsitems', array('name' => 'La Porte', 'text' => 'C\'est comme perdre', 'authorClub' => 'bde'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('DELETE', '/newsitems/le-jeu');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('POST', '/newsitems', array('name' => 'Manger', 'text' => 'C\'est comme perdre', 'authorClub' => 'bda'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('DELETE', '/newsitems/manger');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/clubs/bda', array('icon' => 'test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        // On teste que l'utilisateur puisse modifier son propre profil
        $this->client->request('PATCH', '/users/donat-bb', array('firstName' => 'Benoît'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        // On teste le rajout/la suppression de membre
        $this->client->request('POST', '/clubs/bda/users/dziris', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/clubs/bda/users/dziris', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/ki/users/dziris', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('DELETE', '/clubs/ki/users/dziris', array('role' => 'Test'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);
    }

    public function testFoyerStats()
    {
        // On se présente comme un trouffion de base
        $client = static::createClient();
        $client->request('POST', $this->getUrl('login'), array('username' => 'muzardt', 'password' => 'password'));
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;

        // On teste que n'importe qui ne puisse pas récupérer les statistiques perso
        $this->client->request('GET', '/foyer/statistics/muzardt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertTrue(empty($infos['error']));

        $this->client->request('GET', '/foyer/statistics/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertFalse(empty($infos['error']));
    }

    public function testPonthubStats()
    {
        // On se présente comme un trouffion de base
        $client = static::createClient();
        $client->request('POST', $this->getUrl('login'), array('username' => 'muzardt', 'password' => 'password'));
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;

        // On teste que n'importe qui ne puisse pas récupérer les statistiques perso
        $this->client->request('GET', '/statistics/muzardt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertTrue(empty($infos['error']));

        $this->client->request('GET', '/statistics/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertFalse(empty($infos['error']));
    }
}
