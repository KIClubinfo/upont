<?php
namespace Tests\KI\UserBundle;

use Tests\KI\CoreBundle\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = [
            [200, 'GET', '/clubs'],
            [200, 'GET', '/clubs/ki'],
            [404, 'GET', '/clubs/sddsdqs'],
            [200, 'GET', '/users/VpqtuEGC/calendar'],
            [401, 'POST', '/clubs'],
            [400, 'POST', '/resetting/request'],
            [401, 'PATCH', '/promo/016/pictures']
        ];
        $this->checkRoutes($routes);
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $this->connect('donat-bb', 'password');

        $this->client->request('PATCH', '/clubs/bda', ['icon' => 'test']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        // On teste que l'utilisateur puisse modifier son propre profil

        $this->client->request('PATCH', '/users/donat-bb', ['firstName' => 'Benoît']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
        // On teste le rajout/la suppression de membre

        $this->client->request('POST', '/clubs/bda/users/dziris', ['role' => 'Test']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/clubs/bda/users/dziris', ['role' => 'Test']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/clubs/ki/users/dziris', ['role' => 'Test']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);

        $this->client->request('DELETE', '/clubs/ki/users/dziris', ['role' => 'Test']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 403);
    }

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = [
            [200, 'GET', '/clubs'],
            [200, 'GET', '/clubs/ki'],
            [404, 'GET', '/clubs/sddsdqs'],
            [200, 'GET', '/users'],
            [403, 'POST', '/clubs'],
            [403, 'PATCH', '/users/admissibles'],
            [403, 'PATCH', '/promo/016/pictures'],
            [403, 'POST', '/resetting/request', ['username' => 'admissibles']],
        ];
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = [
            [200, 'GET', '/clubs'],
            [200, 'GET', '/clubs/ki'],
            [200, 'GET', '/users/trancara'],
            [404, 'GET', '/clubs/sddsdqs'],
            [403, 'GET', '/users'],
            [403, 'POST', '/clubs'],
            [403, 'PATCH', '/promo/016/pictures'],
            [403, 'PATCH', '/users/gcc'],
            [204, 'PATCH', '/clubs/gcc', ['fullName' => 'Génie']],
        ];
        $this->checkRoutes($routes);
    }
}
