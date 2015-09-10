<?php
namespace KI\PublicationBundle\Tests;
use KI\CoreBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = array(
            array(401, 'GET', '/newsitems'),
            array(404, 'GET', '/courses/mecanique-des-structures/exercices/test/download'),
            array(200, 'GET', '/users/VpqtuEGC/calendar'),
        );
        $this->checkRoutes($routes);
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $this->connect('donat-bb', 'password');

        // Maintenant on teste quelques trucs
        $this->client->request('POST', '/newsitems/le-beton-c-est-bon/comments');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

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
    }

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = array(
            array(200, 'GET', '/newsitems'),
            array(200, 'GET', '/courses'),
            array(403, 'POST', '/newsitems'),
            array(403, 'POST', '/events'),
            array(403, 'PATCH', '/events/don-giovanni'),
            array(403, 'PATCH', '/newsitems/pulls'),
        );
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = array(
            array(200, 'GET', '/newsitems'),
            array(403, 'GET', '/courses'),
            array(403, 'POST', '/newsitems/le-beton-c-est-bon/comments'),
            array(200, 'GET', '/newsitems/le-beton-c-est-bon/comments')
        );
        $this->checkRoutes($routes);
    }
}
