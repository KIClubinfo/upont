<?php
namespace Tests\App;

use App\Tests\WebTestCase;

class PonthubSecurityTest extends WebTestCase
{
    // Vérifie que les routes non firewallées sont utilisables
    public function testFirewall()
    {
        $this->client = static::createClient();
        $routes = [
            [401, 'GET', '/series/how-i-met-your-mother/episodes/pilot/download'],
        ];
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
        $routes = [
            [204, 'PATCH', '/series/how-i-met-your-mother', ['duration' => 10]],
            [204, 'PATCH', '/movies/pumping-iron', ['duration' => 10]]
        ];
        $this->checkRoutes($routes);
    }

    public function testRoleAdmissible()
    {
        // On se présente comme un admissible
        $this->connect('admissibles', 'password');
        $routes = [
            [404, 'GET', '/clbs/sddsdqs'],
            [200, 'GET', '/series/how-i-met-your-mother/episodes/pilot'],
            [200, 'GET', '/series/how-i-met-your-mother/episodes/pilot/comments'],
            [200, 'GET', '/movies/pumping-iron'],
            [200, 'GET', '/games'],
            [403, 'POST', '/movies/pumping-iron/like'],
            [403, 'DELETE', '/movies/pumping-iron/like'],
            [403, 'POST', '/movies/pumping-iron/comments']
        ];
        $this->checkRoutes($routes);
    }

    public function testRoleExterieur()
    {
        // On se présente comme un extérieur de l'administration
        $this->connect('gcc', 'password');
        $routes = [
            [403, 'GET', '/series/how-i-met-your-mother/episodes/pilot'],
            [403, 'GET', '/movies/pumping-iron'],
            [403, 'GET', '/games'],
            [403, 'POST', '/movies/pumping-iron/like'],
            [403, 'DELETE', '/movies/pumping-iron/like'],
            [403, 'POST', '/movies/pumping-iron/comments'],
            [403, 'GET', '/series/how-i-met-your-mother/episodes/pilot/comments'],
        ];
        $this->checkRoutes($routes);
    }
}
