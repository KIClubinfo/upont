<?php

namespace KI\UpontBundle\Tests;

use KI\UpontBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();

        $routes = array(
            array(200, 'GET', '/clubs'),
            array(401, 'GET', '/newsitems'),
            array(200, 'GET', '/clubs/ki'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(404, 'GET', '/courses/mecanique-des-structures/exercices/test/download'),
            array(401, 'GET', '/series/how-i-met-your-mother/episodes/pilot/download'),
            array(200, 'GET', '/users/VpqtuEGC/calendar'),
            array(401, 'POST', '/clubs'),
            array(400, 'POST', '/resetting/request'),
            array(401, 'PATCH', '/promo/016/pictures'),
        );
        $this->checkRoutes($routes);
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $this->connect('donat-bb', 'password');

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
        $this->connect('muzardt', 'password');

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
        $this->connect('muzardt', 'password');

        // On teste que n'importe qui ne puisse pas récupérer les statistiques perso
        $this->client->request('GET', '/ponthub/statistics/muzardt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertTrue(empty($infos['error']));

        $this->client->request('GET', '/ponthub/statistics/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertFalse(empty($infos['error']));
    }

    public function testRoleJardinier()
    {
        // On se présente comme un jardinier
        $this->connect('vessairc', 'password');
        $routes = array(
            array(204, 'PATCH', '/series/how-i-met-your-mother', array('duration' => 10)),
            array(204, 'PATCH', '/movies/pumping-iron', array('duration' => 10))
        );
        $this->checkRoutes($routes);
    }

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = array(
            /*array(200, 'GET', '/clubs'),
            array(200, 'GET', '/newsitems'),
            array(200, 'GET', '/clubs/ki'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(200, 'GET', '/courses'),
            array(200, 'GET', '/series/how-i-met-your-mother/episodes/pilot'),
            array(200, 'GET', '/movies/pumping-iron'),
            array(200, 'GET', '/games'),
            array(200, 'GET', '/users'),
            array(403, 'POST', '/clubs'),
            array(403, 'POST', '/newsitems'),
            array(403, 'POST', '/events'),
            array(403, 'PATCH', '/events/don-giovanni'),
            array(403, 'PATCH', '/newsitems/pulls'),
            array(403, 'PATCH', '/users/admissibles'),
            array(200, 'GET', '/foyer/statistics'),*/
            array(403, 'PATCH', '/promo/016/pictures'),
            array(403, 'POST', '/resetting/request', array('username' => 'admissibles')),
            array(403, 'POST', '/movies/pumping-iron/like'),
            array(403, 'DELETE', '/movies/pumping-iron/like'),
            array(403, 'POST', '/movies/pumping-iron/comments')
        );
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = array(
            /*array(200, 'GET', '/clubs'),
            array(200, 'GET', '/newsitems'),
            array(200, 'GET', '/clubs/ki'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(403, 'GET', '/courses'),
            array(403, 'GET', '/series/how-i-met-your-mother/episodes/pilot'),
            array(403, 'GET', '/movies/pumping-iron'),
            array(403, 'GET', '/games'),
            array(403, 'GET', '/users'),
            array(403, 'POST', '/clubs'),*/
            array(403, 'GET', '/foyer/statistics'),
            array(403, 'PATCH', '/promo/016/pictures'),
            array(403, 'PATCH', '/users/gcc'),
            array(403, 'PATCH', '/clubs/gcc'),
            array(403, 'POST', '/movies/pumping-iron/like'),
            array(403, 'DELETE', '/movies/pumping-iron/like'),
            array(403, 'POST', '/movies/pumping-iron/comments')
        );
        $this->checkRoutes($routes);
    }
}
