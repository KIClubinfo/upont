<?php
namespace KI\UserBundle\Tests;
use KI\CoreBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = array(
            array(200, 'GET', '/clubs'),
            array(200, 'GET', '/clubs/ki'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(200, 'GET', '/users/VpqtuEGC/calendar'),
            array(401, 'POST', '/clubs'),
            array(400, 'POST', '/resetting/request'),
            array(401, 'PATCH', '/promo/016/pictures')
        );
        $this->checkRoutes($routes);
    }

    public function testClubMembership()
    {
        // On se présente comme un trouffion de base
        $this->connect('donat-bb', 'password');

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

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = array(
            array(200, 'GET', '/clubs'),
            array(200, 'GET', '/clubs/ki'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(200, 'GET', '/users'),
            array(403, 'POST', '/clubs'),
            array(403, 'PATCH', '/users/admissibles'),
            array(403, 'PATCH', '/promo/016/pictures'),
            array(403, 'POST', '/resetting/request', array('username' => 'admissibles')),
        );
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = array(
            array(200, 'GET', '/clubs'),
            array(200, 'GET', '/clubs/ki'),
            array(200, 'GET', '/users/trancara'),
            array(404, 'GET', '/clubs/sddsdqs'),
            array(403, 'GET', '/users'),
            array(403, 'POST', '/clubs'),
            array(403, 'PATCH', '/promo/016/pictures'),
            array(403, 'PATCH', '/users/gcc'),
            array(403, 'PATCH', '/clubs/gcc'),
        );
        $this->checkRoutes($routes);
    }
}
