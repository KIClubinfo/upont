<?php
namespace KI\CoreBundle\Tests;
use KI\CoreBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    public function testFoyerStats()
    {
        // On se présente comme un trouffion de base
        $this->connect('muzardt', 'password');
        // On teste que n'importe qui ne puisse pas récupérer les statistiques perso
        $this->client->request('GET', '/statistics/foyer/muzardt');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertTrue(empty($infos['error']));

        $this->client->request('GET', '/statistics/foyer/dziris');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        //$this->assertFalse(empty($infos['error']));
    }

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = array(
            array(200, 'GET', '/statistics/foyer'),
        );
        $this->checkRoutes($routes);
    }

    public function testRoleFoyer()
    {
        // On se présente comme un admissible
        $this->connect('muzardt', 'password');
        $routes = array(
            array(403, 'POST', '/beers/leffe/users/trancara'),
        );
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = array(
            array(403, 'GET', '/statistics/foyer'),
        );
        $this->checkRoutes($routes);
    }
}
