<?php
namespace KI\CoreBundle\Tests;
use KI\CoreBundle\Tests\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = array(
            array(401, 'GET', '/series/how-i-met-your-mother/episodes/pilot/download'),
        );
        $this->checkRoutes($routes);
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
            array(404, 'GET', '/clbs/sddsdqs'),
            array(200, 'GET', '/series/how-i-met-your-mother/episodes/pilot'),
            array(200, 'GET', '/series/how-i-met-your-mother/episodes/pilot/comments'),
            array(200, 'GET', '/movies/pumping-iron'),
            array(200, 'GET', '/games'),
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
            array(403, 'GET', '/series/how-i-met-your-mother/episodes/pilot'),
            array(403, 'GET', '/movies/pumping-iron'),
            array(403, 'GET', '/games'),
            array(403, 'POST', '/movies/pumping-iron/like'),
            array(403, 'DELETE', '/movies/pumping-iron/like'),
            array(403, 'POST', '/movies/pumping-iron/comments'),
            array(403, 'GET', '/series/how-i-met-your-mother/episodes/pilot/comments'),
        );
        $this->checkRoutes($routes);
    }
}
