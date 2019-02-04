<?php
namespace Tests\App;

use App\Tests\WebTestCase;

class PublicationsSecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = [
            [401, 'GET', '/newsitems'],
            [404, 'GET', '/courses/mecanique-des-structures/exercices/test/download'],
            [200, 'GET', '/users/VpqtuEGC/calendar'],
            [401, 'POST', '/newsitems/le-beton-cest-bon/comments'],
        ];
        $this->checkRoutes($routes);
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $this->connect('donat-bb', 'password');

        // Maintenant on teste quelques trucs
        $this->client->request('POST', '/newsitems/le-beton-cest-bon/comments');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/newsitems', ['name' => 'La Porte', 'text' => 'C\'est comme perdre']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);

        $this->client->request('DELETE', '/newsitems/la-porte');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/newsitems', ['name' => 'La Porte', 'text' => 'C\'est comme perdre', 'authorClub' => 'bde']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('DELETE', '/newsitems/le-jeu');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('POST', '/newsitems', ['name' => 'Manger', 'text' => 'C\'est comme perdre', 'authorClub' => 'bda']);
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
        $routes = [
            [200, 'GET', '/newsitems'],
            [200, 'GET', '/courses'],
            [403, 'POST', '/newsitems'],
            [403, 'POST', '/events'],
            [403, 'PATCH', '/events/don-giovanni'],
            [403, 'PATCH', '/newsitems/pulls'],
        ];
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = [
            [403, 'GET', '/newsitems'],
            [403, 'GET', '/courses'],
            [403, 'POST', '/newsitems/le-beton-cest-bon/comments'],
            [403, 'GET', '/newsitems/le-beton-cest-bon/comments']
        ];
        $this->checkRoutes($routes);
    }
}
