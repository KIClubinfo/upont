<?php

namespace App\Tests;

use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class WebTestCase extends SymfonyWebTestCase
{
    protected $container;
    /* @var Client */
    protected $client;
    protected $authorizationHeaderPrefix = 'Bearer';
    protected $queryParameterName = 'bearer';

    // Crée un client autentifié
    public function __construct()
    {
        parent::__construct();

        // On ne se logge qu'une fois pour tous les tests
        $path = __DIR__ . '/../var/cache/token';
        if (!file_exists($path)) {
            $client = static::createClient();
            $client->request(
                'POST',
                '/login',
                ['username' => 'trancara', 'password' => 'password']
            );
            $response = $client->getResponse();
            $data = json_decode($response->getContent(), true);

            try {
                $this->assertArrayHasKey('token', $data);
            } catch (ExpectationFailedException $exception) {
                print_r($data);
                throw $exception;
            }
            file_put_contents($path, $data['token']);
        }
    }

    public function setUp()
    {
        parent::setUp();

        $client = $this->createClient();
        $path = __DIR__ . '/../var/cache/token';
        $client->setServerParameter('HTTP_Authorization', $this->authorizationHeaderPrefix . ' ' . file_get_contents($path));
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

    public function connect($username, $password)
    {
        $client = static::createClient();
        $client->request('POST', '/login', ['username' => $username, 'password' => $password]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;
    }

    // Checke tout un tableau de routes pour aller plus vite
    public function checkRoutes($routes)
    {
        foreach ($routes as $route) {
            echo "\n" . $route[1] . ' ' . $route[2];
            if (isset($route[3]))
                $this->client->request($route[1], $route[2], $route[3]);
            else
                $this->client->request($route[1], $route[2]);
            $this->assertJsonResponse($this->client->getResponse(), $route[0]);
        }
    }
}
