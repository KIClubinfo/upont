<?php

namespace KI\UpontBundle\Tests;

use Symfony\Component\HttpFoundation\Response;
use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;

abstract class WebTestCase extends LiipWebTestCase
{
    protected $container;
    protected $client;
    protected $authorizationHeaderPrefix = 'Bearer';
    protected $queryParameterName = 'bearer';

    // Crée un client autentifié
    public function __construct()
    {
        parent::__construct();

        // On ne se logge qu'une fois pour tous les tests
        $path = __DIR__.'/../../../../app/cache/token';
        if (!file_exists($path)) {
            $client = static::createClient();
            $client->request('POST', $this->getUrl('login'), array('username' => 'trancara', 'password' => 'password'));
            $response = $client->getResponse();
            $data = json_decode($response->getContent(), true);
            $this->assertArrayHasKey('token', $data);
            file_put_contents($path, $data['token']);
        }

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', $this->authorizationHeaderPrefix.' '.file_get_contents($path));
        $this->client = $client;
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200, $checkValidJson = false, $contentType = 'application/json')
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );

        // On se fout de ce qui est retourné si rien n'est retourné
        if ($statusCode != 204 && $checkValidJson) {
            $this->assertTrue(
                $response->headers->contains('Content-Type', $contentType),
                $response->headers
            );
        }

        if ($checkValidJson) {
            $decode = json_decode($response->getContent(), true);
            $this->assertTrue(
                ($decode !== null && $decode !== false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}
